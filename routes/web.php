<?php

use Illuminate\Support\Facades\Route;
use PaymentIntegrator\PaymentGatewayManager;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-checkout', function (PaymentGatewayManager $payments) {
    // amount in MINOR units: 1999 = $19.99
    $checkout = $payments->createCheckout(1999, 'USD', 'Pro Plan', ['order_id' => '123']);
    return response()->json($checkout);
});
