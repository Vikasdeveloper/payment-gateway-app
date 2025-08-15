# Laravel Payment Gateway Integrator

Unified, extensible **payment gateway abstraction** for Laravel with a single API covering **Stripe, Razorpay, PayPal, PayU**, and a **mock mode** for safe local testing. Includes webhook verification and a clean driver architecture.

## ✨ Highlights
- Single interface for create checkout, capture, refund, and webhook verification.
- Drivers: Stripe, Razorpay, PayPal, PayU (with mock mode).
- Publishable config with env-driven credentials.
- Minimal routes for webhooks.
- Designed as a **Laravel package** you can drop into any app.

## 📦 Installation (as local package)
1. Copy this folder into your Laravel project, e.g. `packages/laravel-payment-gateway-integrator`.
2. In your Laravel `composer.json`, add a path repository:
   ```json
   {
     "repositories": [
       { "type": "path", "url": "packages/laravel-payment-gateway-integrator" }
     ]
   }
   ```
3. Require it via Composer:
   ```bash
   composer require yourname/laravel-payment-gateway-integrator:* --dev
   ```
   (Composer will symlink to the path.)

4. Publish config:
   ```bash
   php artisan vendor:publish --provider="PaymentIntegrator\PaymentGatewayServiceProvider" --tag=config
   ```

## ⚙️ Env / Config
Set in `.env`:
```
PAYMENT_DRIVER=stripe
PAYMENT_MOCK=true
PAYMENT_CURRENCY=USD

STRIPE_SECRET=sk_test_xxx
STRIPE_KEY=pk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
STRIPE_SUCCESS_URL=http://localhost/success
STRIPE_CANCEL_URL=http://localhost/cancel

RAZORPAY_KEY_ID=rzp_test_xxx
RAZORPAY_KEY_SECRET=xxx
RAZORPAY_WEBHOOK_SECRET=whsec_xxx

PAYPAL_CLIENT_ID=xxx
PAYPAL_CLIENT_SECRET=xxx
PAYPAL_ENV=sandbox

PAYU_KEY=xxx
PAYU_SALT=xxx
PAYU_ENV=test
```

## 🧪 Quick Usage
```php
use PaymentIntegrator\PaymentGatewayManager;

// Resolve via container or facade:
$gateway = app(PaymentGatewayManager::class);

// Create checkout (amount in minor units e.g. cents/paise)
$checkout = $gateway->createCheckout(1999, 'USD', 'Pro Plan', ['order_id' => '123']);
// => ['id' => '...', 'url' => '...'] (Stripe/PayPal) or just an order id (Razorpay)

// Capture (when applicable)
$gateway->capture('pi_or_payment_id');

// Refund (optionally with partial amount)
$gateway->refund('pi_or_payment_id', 500);

// Verify webhook inside your controller (already wired in this package)
```

## 🌐 Webhook Endpoint
This package registers a webhook route:
```
POST /payments/webhook/{driver}
```
Example:
- Stripe: `POST /payments/webhook/stripe`
- Razorpay: `POST /payments/webhook/razorpay`

You can change the prefix in `config/payment-gateways.php`.

## 🧰 Real Gateways vs Mock
- Set `PAYMENT_MOCK=true` for local development. No external calls are made.
- To use real gateways, install their SDKs and set `PAYMENT_MOCK=false`:
  ```bash
  composer require stripe/stripe-php razorpay/razorpay paypal/paypal-checkout-sdk
  ```

## 🗂 Structure
```
src/
  Contracts/PaymentGateway.php
  Gateways/{StripeGateway,RazorpayGateway,PaypalGateway,PayUGateway}.php
  Http/Controllers/WebhookController.php
  PaymentGatewayManager.php
  PaymentGatewayServiceProvider.php
  Facades/PaymentGatewayManager.php
config/payment-gateways.php
routes/web.php
```

## 🧭 Notes
- Amounts are passed as **minor units** (e.g., 1999 = $19.99).
- Stripe driver implements real calls if SDK present; others include mock + stubs.
- Extend by adding your own driver class implementing `Contracts\PaymentGateway` and a `createXDriver()` in `PaymentGatewayManager`.

## 📄 License
MIT
