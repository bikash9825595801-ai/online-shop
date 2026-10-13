<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\AuditService;
use App\Services\Payments\EsewaService;
use App\Services\Payments\KhaltiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private EsewaService $esewa,
        private KhaltiService $khalti,
    ) {
        $this->middleware('auth');
        $this->middleware('role:customer');
    }

    /**
     * Entry point after checkout. Branches on the chosen payment method.
     */
    public function initiate(Order $order): RedirectResponse|View
    {
        $this->authorizeOrder($order);

        $payment = $order->payment;

        if (! $payment || $payment->isPaid()) {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'This order has already been paid or does not need payment.');
        }

        switch ($payment->payment_method) {
            case Payment::METHOD_ESEWA:
                $fields = $this->esewa->buildForm(
                    $order,
                    route('payment.esewa.success'),
                    route('payment.esewa.failure'),
                );

                return view('payment.esewa-qr', [
                    'order' => $order,
                    'fields' => $fields,
                    'esewaFormUrl' => $this->esewa->formUrl(),
                ]);

            case Payment::METHOD_KHALTI:
            case Payment::METHOD_CARD:
                return $this->startKhalti($order, $payment->payment_method);

            case Payment::METHOD_BANK:
                return view('payment.bank', ['order' => $order]);

            default: // cash on delivery
                return redirect()->route('orders.show', $order->id);
        }
    }

    // ---------------------------------------------------------------------
    // eSewa (QR)
    // ---------------------------------------------------------------------

    /**
     * Receive the customer's eSewa transaction code after scanning the QR.
     */
    public function esewaQr(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'transaction_code' => ['required', 'string', 'max:100'],
        ]);

        if ($order->payment) {
            $order->payment()->update([
                'transaction_id' => $validated['transaction_code'],
                'status' => Payment::STATUS_PENDING,
                'meta' => array_merge($order->payment->meta ?? [], [
                    'esewa_txn_code' => $validated['transaction_code'],
                    'submitted_at' => now()->toIso8601String(),
                ]),
            ]);
        }

        AuditService::log('eSewa QR payment submitted', 'Payments', "eSewa transaction code submitted for {$order->order_number}.");

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'eSewa transaction code received. Your payment will be verified shortly.');
    }

    public function esewaSuccess(Request $request): RedirectResponse
    {
        $payload = $this->esewa->verifyCallback($request->query('data'));

        if (! $payload) {
            AuditService::log('Payment callback', 'Payments', 'eSewa callback signature could not be verified.');

            return redirect()->route('orders.index')->with('error', 'eSewa payment verification failed.');
        }

        $order = Order::with('payment')->where('order_number', $payload['transaction_uuid'])->first();

        if (! $order) {
            return redirect()->route('orders.index')->with('error', 'Payment received but order was not found.');
        }

        // Double-check with eSewa's status API before marking as paid.
        $status = $this->esewa->status($order);
        $confirmed = strtoupper((string) ($status['status'] ?? '')) === 'COMPLETE'
            || strtoupper((string) ($payload['status'] ?? '')) === 'COMPLETE';

        if (! $confirmed) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'eSewa payment is still pending confirmation.');
        }

        $this->markPaid(
            $order,
            (string) ($payload['transaction_code'] ?? $payload['transaction_uuid'] ?? null),
            ['gateway' => 'esewa', 'status' => $status],
        );

        return redirect()->route('orders.show', $order->id)
            ->with('success', "Payment successful for {$order->order_number}. Thank you!");
    }

    public function esewaFailure(Request $request): RedirectResponse
    {
        $payload = $this->esewa->verifyCallback($request->query('data'));
        $order = $payload
            ? Order::where('order_number', $payload['transaction_uuid'])->first()
            : null;

        AuditService::log('Payment failed', 'Payments', 'eSewa payment failed'.($order ? " for {$order->order_number}" : '').'.');

        if ($order) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'eSewa payment was not completed. You can try again.');
        }

        return redirect()->route('orders.index')->with('error', 'eSewa payment was not completed.');
    }

    // ---------------------------------------------------------------------
    // Khalti (also used for card payments)
    // ---------------------------------------------------------------------

    private function startKhalti(Order $order, string $method): RedirectResponse
    {
        $label = $method === Payment::METHOD_CARD
            ? 'Card - '.$order->order_number
            : (string) $order->order_number;

        try {
            $session = $this->khalti->initiate($order, route('payment.khalti.callback'), $label);

            if ($order->payment) {
                $order->payment()->update([
                    'meta' => array_merge($order->payment->meta ?? [], ['pidx' => $session['pidx']]),
                ]);
            }

            return redirect()->away($session['payment_url']);
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Could not start Khalti payment. Please try again later.');
        }
    }

    public function khaltiCallback(Request $request): RedirectResponse
    {
        $order = $request->filled('purchase_order_id')
            ? Order::with('payment')->find($request->query('purchase_order_id'))
            : null;

        $pidx = (string) $request->query('pidx', '');

        $lookup = $pidx !== '' ? $this->khalti->lookup($pidx) : null;

        if ($order && $this->khalti->isCompleted($lookup)) {
            $this->markPaid(
                $order,
                (string) ($lookup['transaction_id'] ?? $request->query('transaction_id', '')),
                ['gateway' => $lookup['provider'] ?? 'khalti', 'pidx' => $pidx, 'lookup' => $lookup],
            );

            return redirect()->route('orders.show', $order->id)
                ->with('success', "Payment successful for {$order->order_number}. Thank you!");
        }

        if ($order) {
            AuditService::log('Payment failed', 'Payments', "Khalti payment not completed for {$order->order_number}.");

            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Khalti payment was not completed. You can try again.');
        }

        return redirect()->route('orders.index')->with('error', 'Khalti payment callback could not be matched.');
    }

    // ---------------------------------------------------------------------
    // Bank transfer proof
    // ---------------------------------------------------------------------

    public function bankProof(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($order);

        $validated = $request->validate([
            'proof' => ['required', 'file', 'image', 'max:4096'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $path = $request->file('proof')->store('bank-proofs', 'public');

        if ($order->payment) {
            $order->payment()->update([
                'proof_path' => $path,
                'transaction_id' => $validated['reference'] ?? $order->payment->transaction_id,
                'meta' => array_merge($order->payment->meta ?? [], [
                    'bank_reference' => $validated['reference'] ?? null,
                    'proof_path' => $path,
                    'submitted_at' => now()->toIso8601String(),
                ]),
            ]);
        }

        AuditService::log('Payment proof uploaded', 'Payments', "Bank transfer proof submitted for {$order->order_number}.");

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Bank transfer proof submitted. It will be verified shortly.');
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function authorizeOrder(Order $order): void
    {
        abort_if($order->customer_id !== Auth::id(), 403);
    }

    private function markPaid(Order $order, ?string $transactionId, array $meta = []): void
    {
        $order->update(['payment_status' => Order::PAYMENT_PAID]);

        if ($order->payment) {
            $order->payment()->update([
                'status' => Payment::STATUS_PAID,
                'transaction_id' => $transactionId ?: $order->payment->transaction_id,
                'paid_at' => now(),
                'meta' => array_merge($order->payment->meta ?? [], $meta),
            ]);
        }

        AuditService::log(
            'Payment received',
            'Payments',
            "Order {$order->order_number} paid via ".($order->payment->methodLabel() ?? 'gateway').'.',
        );
    }
}
