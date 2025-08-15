<?php

namespace PaymentIntegrator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use PaymentIntegrator\PaymentGatewayManager;

class WebhookController extends BaseController
{
    public function handle(string $driver, Request $request, PaymentGatewayManager $manager)
    {
        // Use the specified driver for this webhook endpoint
        $gateway = $manager->driver($driver);

        $verification = $gateway->verifyWebhook($request->getContent(), $request->headers->all());

        if (!$verification['ok']) {
            return response()->json(['ok' => false, 'error' => $verification['error'] ?? 'Invalid'], 400);
        }

        // You can dispatch events/jobs here based on $verification['event']
        // For demo:
        return response()->json(['ok' => true, 'event' => $verification['event'], 'data' => $verification['data'] ?? null]);
    }
}
