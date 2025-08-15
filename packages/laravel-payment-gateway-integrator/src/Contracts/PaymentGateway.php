<?php

namespace PaymentIntegrator\Contracts;

interface PaymentGateway
{
    /**
     * Creates a checkout session or payment order and returns a redirect/url and id.
     * @param int|float $amount Minor or major units depending on gateway; we standardize to cents/paise using $currency exponent.
     * @param string $currency
     * @param string $description
     * @param array $metadata
     * @return array{ id:string, url?:string, status?:string, raw?:mixed }
     */
    public function createCheckout($amount, string $currency, string $description = '', array $metadata = []): array;

    /**
     * Capture/confirm a payment when applicable.
     * @param string $paymentId
     * @return array
     */
    public function capture(string $paymentId): array;

    /**
     * Refund a payment (partial supported via amount).
     * @param string $paymentId
     * @param int|float|null $amount
     * @return array
     */
    public function refund(string $paymentId, $amount = null): array;

    /**
     * Verify webhook signature and return parsed payload.
     * @param string $rawBody
     * @param array $headers
     * @return array{ ok:bool, event?:string, data?:mixed, error?:string }
     */
    public function verifyWebhook(string $rawBody, array $headers = []): array;
}
