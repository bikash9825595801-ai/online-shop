<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | All prices in the application are stored in NPR (Nepali Rupees).
    |
    */
    'currency' => env('PAYMENT_CURRENCY', 'NPR'),

    /*
    |--------------------------------------------------------------------------
    | eSewa ePay v2
    |--------------------------------------------------------------------------
    |
    | Defaults point at the official eSewa sandbox (test) environment.
    | Replace the merchant code and secret key with production credentials
    | when you are ready to accept live payments.
    |
    */
    'esewa' => [
        'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),
        'secret_key' => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'),
        'form_url' => env('ESEWA_FORM_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
        'status_url' => env('ESEWA_STATUS_URL', 'https://rc-epay.esewa.com.np/api/epay/transaction/status/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Khalti ePayment v2
    |--------------------------------------------------------------------------
    |
    | Defaults point at the official Khalti sandbox (test) environment. The
    | public sandbox secret key is safe to use for development only.
    |
    */
    'khalti' => [
        'secret_key' => env('KHALTI_SECRET_KEY', 'live_secret_key_68791341fdd94846a146f0457ff7b455'),
        'base_url' => env('KHALTI_BASE_URL', 'https://a.khalti.com/api/v2/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe (card payments)
    |--------------------------------------------------------------------------
    |
    | Card payments are processed through Stripe Checkout in test mode.
    | Add your Stripe test keys to enable card payments.
    |
    */
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank transfer details
    |--------------------------------------------------------------------------
    |
    | Displayed to the customer on the bank-transfer payment page.
    |
    */
    'bank' => [
        'bank_name' => env('BANK_NAME', 'Nabil Bank Ltd.'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'Online Shopping Management System'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '01700000000000'),
        'branch' => env('BANK_BRANCH', 'Kathmandu, Nepal'),
    ],

];
