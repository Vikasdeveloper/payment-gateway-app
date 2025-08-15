<?php

namespace PaymentIntegrator\Gateways;

use PaymentIntegrator\Contracts\PaymentGateway;

class PayUGateway implements PaymentGateway
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
                'id' => 'payu_' . bin2hex(random_bytes(6)),
                'status' => 'created',
                'raw' => ['mock' => true],
            ];
        }
        throw new \RuntimeException('Implement PayU createCheckout or use mock mode.');
    }

    public function capture(string $paymentId): array
    {
        if ($this->mock) {
            return ['id' => $paymentId, 'status' => 'captured', 'raw' => ['mock' => true]];
        }
        throw new \RuntimeException('Implement PayU capture or use mock mode.');
    }

    public function refund(string $paymentId, $amount = null): array
    {
        if ($this->mock) {
            return ['id' => 'payu_ref_' . bin2hex(random_bytes(6)), 'status' => 'success', 'raw' => ['mock' => true]];
        }
        throw new \RuntimeException('Implement PayU refund or use mock mode.');
    }

    public function verifyWebhook(string $rawBody, array $headers = []): array
    {
        if ($this->mock) {
            return ['ok' => true, 'event' => 'payment.success', 'data' => ['mock' => true]];
        }
        return ['ok' => false, 'error' => 'Not implemented'];
    }
}
