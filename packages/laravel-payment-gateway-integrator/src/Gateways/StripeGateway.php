<?php

namespace PaymentIntegrator\Gateways;

use PaymentIntegrator\Contracts\PaymentGateway;

class StripeGateway implements PaymentGateway
{
    protected array $config;
    protected bool $mock;

    public function __construct(array $config = [], bool $mock = true)
    {
        $this->config = $config;
        $this->mock = $mock;
    }

    public function createCheckout($amount, string $currency, string $description = '', array $metadata = []): array
    {
        if ($this->mock) {
            return [
                'id' => 'cs_test_' . bin2hex(random_bytes(6)),
                'url' => 'https://payments.local/stripe/checkout/' . bin2hex(random_bytes(4)),
                'status' => 'created',
                'raw' => ['mock' => true]
            ];
        }

        if (!class_exists(\Stripe\Stripe::class)) {
            throw new \RuntimeException('stripe/stripe-php is not installed. Run composer require stripe/stripe-php');
        }

        \Stripe\Stripe::setApiKey($this->config['secret'] ?? null);

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'unit_amount' => (int)$amount,
                    'product_data' => ['name' => $description ?: 'Payment']
                ],
                'quantity' => 1,
            ]],
            'success_url' => $this->config['success_url'] ?? 'http://localhost/success',
            'cancel_url' => $this->config['cancel_url'] ?? 'http://localhost/cancel',
            'metadata' => $metadata,
        ]);

        return ['id' => $session->id, 'url' => $session->url, 'status' => $session->status ?? 'created', 'raw' => $session];
    }

    public function capture(string $paymentId): array
    {
        if ($this->mock) {
            return ['id' => $paymentId, 'status' => 'captured', 'raw' => ['mock' => true]];
        }

        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentId);
        $paymentIntent->capture();
        return ['id' => $paymentIntent->id, 'status' => $paymentIntent->status, 'raw' => $paymentIntent];
    }

    public function refund(string $paymentId, $amount = null): array
    {
        if ($this->mock) {
            return ['id' => 're_' . bin2hex(random_bytes(6)), 'status' => 'succeeded', 'raw' => ['mock' => true]];
        }

        $params = ['payment_intent' => $paymentId];
        if (!is_null($amount)) $params['amount'] = (int)$amount;

        $refund = \Stripe\Refund::create($params);
        return ['id' => $refund->id, 'status' => $refund->status, 'raw' => $refund];
    }

    public function verifyWebhook(string $rawBody, array $headers = []): array
    {
        if ($this->mock) {
            return ['ok' => true, 'event' => 'payment_intent.succeeded', 'data' => ['mock' => true]];
        }

        $secret = $this->config['webhook_secret'] ?? null;
        $sigHeader = $headers['Stripe-Signature'] ?? $headers['stripe-signature'] ?? null;

        if (!$secret || !$sigHeader) {
            return ['ok' => false, 'error' => 'Missing Stripe webhook secret or signature header.'];
        }

        try {
            $event = \Stripe\Webhook::constructEvent($rawBody, $sigHeader, $secret);
            return ['ok' => true, 'event' => $event->type, 'data' => $event->data];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
