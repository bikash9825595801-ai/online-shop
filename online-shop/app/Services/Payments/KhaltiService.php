<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class KhaltiService
{
    private function config(): array
    {
        return config('payment.khalti');
    }

    private function headers(): array
    {
        return ['Authorization' => 'key '.$this->config()['secret_key']];
    }

    /**
     * Start a Khalti ePayment session and return the hosted payment URL.
     *
     * @return array{pidx: string, payment_url: string}
     */
    public function initiate(Order $order, string $returnUrl, string $label = ''): array
    {
        $response = Http::withHeaders($this->headers())
            ->post(rtrim((string) $this->config()['base_url'], '/').'/epayment/initiate/', [
                'return_url' => $returnUrl,
                'website_url' => config('app.url'),
                'amount' => (int) round(((float) $order->total_amount) * 100), // Khalti uses paisa
                'purchase_order_id' => (string) $order->id,
                'purchase_order_name' => $label !== '' ? $label : (string) $order->order_number,
                'customer_info' => [
                    'name' => $order->customerName(),
                    'email' => $order->customerEmail(),
                    'phone' => $order->phone,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Khalti: '.$response->body());
        }

        $payload = $response->json();

        if (empty($payload['payment_url']) || empty($payload['pidx'])) {
            throw new RuntimeException('Khalti: unexpected response '.json_encode($payload));
        }

        return [
            'pidx' => (string) $payload['pidx'],
            'payment_url' => (string) $payload['payment_url'],
        ];
    }

    /**
     * Look up the authoritative status of a payment session by pidx.
     *
     * @return array<string, mixed>|null
     */
    public function lookup(string $pidx): ?array
    {
        $response = Http::withHeaders($this->headers())
            ->post(rtrim((string) $this->config()['base_url'], '/').'/epayment/lookup/', [
                'pidx' => $pidx,
            ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function isCompleted(?array $lookup): bool
    {
        if (! $lookup) {
            return false;
        }

        $status = strtolower((string) ($lookup['status'] ?? ''));

        return in_array($status, ['completed', 'complete', 'success'], true);
    }
}
