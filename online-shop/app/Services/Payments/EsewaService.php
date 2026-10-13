<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class EsewaService
{
    private function config(): array
    {
        return config('payment.esewa');
    }

    public function merchantCode(): string
    {
        return (string) $this->config()['merchant_code'];
    }

    public function formUrl(): string
    {
        return (string) $this->config()['form_url'];
    }

    public function sign(string $signingString): string
    {
        return base64_encode(
            hash_hmac('sha256', $signingString, (string) $this->config()['secret_key'], true)
        );
    }

    /**
     * Build the eSewa v2 signing string from a set of fields.
     */
    public function signingString(string $totalAmount, string $transactionUuid, string $productCode): string
    {
        return sprintf(
            'total_amount=%s,transaction_uuid=%s,product_code=%s',
            $totalAmount,
            $transactionUuid,
            $productCode
        );
    }

    /**
     * Build the auto-submitting checkout form payload.
     *
     * @return array<string, string>
     */
    public function buildForm(Order $order, string $successUrl, string $failureUrl): array
    {
        $total = number_format((float) $order->total_amount, 2, '.', '');

        $fields = [
            'amount' => $total,
            'tax_amount' => '0.00',
            'total_amount' => $total,
            'transaction_uuid' => (string) $order->order_number,
            'product_code' => $this->merchantCode(),
            'product_service_charge' => '0.00',
            'product_delivery_charge' => '0.00',
            'success_url' => $successUrl,
            'failure_url' => $failureUrl,
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
        ];

        $fields['signature'] = $this->sign(
            $this->signingString($fields['total_amount'], $fields['transaction_uuid'], $fields['product_code'])
        );

        return $fields;
    }

    /**
     * Verify the base64 payload returned by eSewa on the success/failure callback.
     *
     * @return array<string, mixed>|null
     */
    public function verifyCallback(?string $data): ?array
    {
        if (! $data) {
            return null;
        }

        $json = base64_decode($data, true);
        if (! is_string($json)) {
            return null;
        }

        $payload = json_decode($json, true);
        if (! is_array($payload) || empty($payload['signature'])) {
            return null;
        }

        $signedNames = explode(',', (string) ($payload['signed_field_names'] ?? ''));
        $parts = [];
        foreach ($signedNames as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $parts[] = $name.'='.($payload[$name] ?? '');
        }

        $expected = $this->sign(implode(',', $parts));
        if (! hash_equals($expected, (string) $payload['signature'])) {
            return null;
        }

        if (($payload['product_code'] ?? '') !== $this->merchantCode()) {
            return null;
        }

        return $payload;
    }

    /**
     * Server-side transaction status check (used to confirm completion).
     *
     * @return array<string, mixed>|null
     */
    public function status(Order $order): ?array
    {
        $response = Http::get($this->config()['status_url'], [
            'product_code' => $this->merchantCode(),
            'total_amount' => number_format((float) $order->total_amount, 2, '.', ''),
            'transaction_uuid' => (string) $order->order_number,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
