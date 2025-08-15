<?php

use Illuminate\Support\Facades\Route;
use PaymentIntegrator\Http\Controllers\WebhookController;

Route::post(config('payment-gateways.webhook_prefix') . '/{driver}', [WebhookController::class, 'handle'])
    ->name('payments.webhook');
