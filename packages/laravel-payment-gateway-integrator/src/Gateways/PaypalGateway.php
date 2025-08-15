<?php

namespace PaymentIntegrator\Gateways;

use PaymentIntegrator\Contracts\PaymentGateway;

class PaypalGateway implements PaymentGateway
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
                'id' => 'pp_' . bin2hex(random_bytes(6)),
                'url' => 'https://payments.local/paypal/checkout/' . bin2hex(random_bytes(4)),
                'status' => 'created',
                'raw' => ['mock' => true],
            ];
        }

        // For real integration use PayPal Checkout SDK (Orders API) here.
        throw new \RuntimeException('Real PayPal integration not implemented in this package. Enable mock=true or add SDK flow.');
    }

    public function capture(string $paymentId): array
    {
        if ($this->mock) {
            return ['id' => $paymentId, 'status' => 'COMPLETED', 'raw' => ['mock' => true]];
        }
        throw new \RuntimeException('Implement PayPal capture via Orders API.');
    }

    public function refund(string $paymentId, $amount = null): array
    {
        if ($this->mock) {
            return ['id' => 'pp_ref_' . bin2hex(random_bytes(6)), 'status' => 'COMPLETED', 'raw' => ['mock' => true]];
        }
        throw new \RuntimeException('Implement PayPal refund via Payments API.');
    }

    public function verifyWebhook(string $rawBody, array $headers = []): array
    {
        if ($this->mock) {
            return ['ok' => true, 'event' => 'CHECKOUT.ORDER.APPROVED', 'data' => ['mock' => true]];
        }
        // Real verification would call PayPal to verify transmission
        return ['ok' => false, 'error' => 'Not implemented'];
    }
}
