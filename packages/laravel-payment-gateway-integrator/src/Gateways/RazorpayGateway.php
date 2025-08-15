<?php

namespace PaymentIntegrator\Gateways;

use PaymentIntegrator\Contracts\PaymentGateway;

class RazorpayGateway implements PaymentGateway
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
                'id' => 'order_' . bin2hex(random_bytes(6)),
                'status' => 'created',
                'raw' => ['mock' => true],
            ];
        }

        if (!class_exists(\Razorpay\Api\Api::class)) {
            throw new \RuntimeException('razorpay/razorpay is not installed. Run composer require razorpay/razorpay');
        }

        $api = new \Razorpay\Api\Api($this->config['key_id'] ?? '', $this->config['key_secret'] ?? '');
        $order = $api->order->create([
            'amount' => (int)$amount,
            'currency' => strtoupper($currency),
            'receipt' => $metadata['receipt'] ?? ('rcpt_' . bin2hex(random_bytes(4))),
            'notes' => $metadata,
        ]);

        return ['id' => $order['id'], 'status' => $order['status'] ?? 'created', 'raw' => $order];
    }

    public function capture(string $paymentId): array
    {
        if ($this->mock) {
            return ['id' => $paymentId, 'status' => 'captured', 'raw' => ['mock' => true]];
        }

        $api = new \Razorpay\Api\Api($this->config['key_id'] ?? '', $this->config['key_secret'] ?? '');
        $payment = $api->payment->fetch($paymentId)->capture(['amount' => null]);
        return ['id' => $payment['id'], 'status' => $payment['status'], 'raw' => $payment];
    }

    public function refund(string $paymentId, $amount = null): array
    {
        if ($this->mock) {
            return ['id' => 'rfnd_' . bin2hex(random_bytes(6)), 'status' => 'processed', 'raw' => ['mock' => true]];
        }

        $api = new \Razorpay\Api\Api($this->config['key_id'] ?? '', $this->config['key_secret'] ?? '');
        $params = [];
        if (!is_null($amount)) $params['amount'] = (int)$amount;
        $refund = $api->payment->fetch($paymentId)->refund($params);
        return ['id' => $refund['id'], 'status' => $refund['status'] ?? 'processed', 'raw' => $refund];
    }

    public function verifyWebhook(string $rawBody, array $headers = []): array
    {
        if ($this->mock) {
            return ['ok' => true, 'event' => 'payment.captured', 'data' => ['mock' => true]];
        }

        $signature = $headers['X-Razorpay-Signature'] ?? $headers['x-razorpay-signature'] ?? null;
        $secret = $this->config['webhook_secret'] ?? null;
        if (!$signature || !$secret) {
            return ['ok' => false, 'error' => 'Missing signature/secret'];
        }

        try {
            $expected = hash_hmac('sha256', $rawBody, $secret);
            if (!hash_equals($expected, $signature)) {
                return ['ok' => false, 'error' => 'Invalid signature'];
            }
            $payload = json_decode($rawBody, true);
            return ['ok' => true, 'event' => $payload['event'] ?? 'unknown', 'data' => $payload];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
