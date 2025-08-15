<?php

namespace PaymentIntegrator;

use Illuminate\Support\Manager;
use PaymentIntegrator\Contracts\PaymentGateway;
use PaymentIntegrator\Gateways\StripeGateway;
use PaymentIntegrator\Gateways\RazorpayGateway;
use PaymentIntegrator\Gateways\PaypalGateway;
use PaymentIntegrator\Gateways\PayUGateway;

class PaymentGatewayManager extends Manager implements PaymentGateway
{
    public function getDefaultDriver()
    {
        return $this->config->get('payment-gateways.default', 'stripe');
    }

    protected function createStripeDriver(): PaymentGateway
    {
        return new StripeGateway($this->config->get('payment-gateways.gateways.stripe', []), (bool)$this->config->get('payment-gateways.mock', true));
    }

    protected function createRazorpayDriver(): PaymentGateway
    {
        return new RazorpayGateway($this->config->get('payment-gateways.gateways.razorpay', []), (bool)$this->config->get('payment-gateways.mock', true));
    }

    protected function createPaypalDriver(): PaymentGateway
    {
        return new PaypalGateway($this->config->get('payment-gateways.gateways.paypal', []), (bool)$this->config->get('payment-gateways.mock', true));
    }

    protected function createPayuDriver(): PaymentGateway
    {
        return new PayUGateway($this->config->get('payment-gateways.gateways.payu', []), (bool)$this->config->get('payment-gateways.mock', true));
    }

    // Proxy interface methods to the selected driver
    public function createCheckout($amount, string $currency, string $description = '', array $metadata = []): array
    { return $this->driver()->createCheckout($amount, $currency, $description, $metadata); }

    public function capture(string $paymentId): array
    { return $this->driver()->capture($paymentId); }

    public function refund(string $paymentId, $amount = null): array
    { return $this->driver()->refund($paymentId, $amount); }

    public function verifyWebhook(string $rawBody, array $headers = []): array
    { return $this->driver()->verifyWebhook($rawBody, $headers); }
}
