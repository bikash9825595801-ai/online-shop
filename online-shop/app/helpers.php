<?php

if (! function_exists('product_image_url')) {
    /**
     * Resolve a stored or remote image to a usable URL.
     */
    function product_image_url(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        return asset('storage/' . $image);
    }
}

if (! function_exists('order_status_badge')) {
    /**
     * Bootstrap badge class for an order status.
     */
    function order_status_badge(string $status): string
    {
        return match ($status) {
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-dark',
            'processing' => 'bg-primary',
            'shipped' => 'bg-indigo',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}

if (! function_exists('payment_status_badge')) {
    function payment_status_badge(string $status): string
    {
        return match ($status) {
            'paid' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'refunded' => 'bg-secondary',
            'failed' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}

if (! function_exists('api_response')) {
    /**
     * Standard JSON envelope used by the mobile API.
     */
    function api_response(bool $success, string $message, mixed $data = null, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
