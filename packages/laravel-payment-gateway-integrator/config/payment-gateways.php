<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "stripe", "razorpay", "paypal", "payu"
    |
    */
    'default' => env('PAYMENT_DRIVER', 'stripe'),

    /*
    | Toggle safe mock mode for local/dev without hitting real gateways.
    */
    'mock' => env('PAYMENT_MOCK', true),

    'currency' => env('PAYMENT_CURRENCY', 'USD'),

    'gateways' => [
        'stripe' => [
            'secret' => env('STRIPE_SECRET'),
            'public' => env('STRIPE_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'success_url' => env('STRIPE_SUCCESS_URL', 'http://localhost/success'),
            'cancel_url' => env('STRIPE_CANCEL_URL', 'http://localhost/cancel'),
        ],
        'razorpay' => [
            'key_id' => env('RAZORPAY_KEY_ID'),
            'key_secret' => env('RAZORPAY_KEY_SECRET'),
            'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'environment' => env('PAYPAL_ENV', 'sandbox'),
        ],
        'payu' => [
            'key' => env('PAYU_KEY'),
            'salt' => env('PAYU_SALT'),
            'env' => env('PAYU_ENV', 'test'),
        ]
    ],

    /*
    | Optionally map webhook routes under this prefix.
    */
    'webhook_prefix' => 'payments/webhook',

];
