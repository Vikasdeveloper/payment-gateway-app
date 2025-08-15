# Laravel Payment Gateway App

A Laravel-based application that integrates multiple payment gateways (Stripe, PayPal, Razorpay, etc.) via the **Laravel Payment Gateway Integrator** package.

---

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Vikasdeveloper/payment-gateway-app.git
   cd payment-gateway-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up your `.env` file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   - Update `.env` with your database credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=payment_gateway_db
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Define your Payment Gateway in `.env`**
   - Add the following keys depending on the gateway you are using:
     ```env
     PAYMENT_GATEWAY=stripe  # Options: stripe, paypal, razorpay, etc.

     # Stripe
     STRIPE_KEY=your_stripe_publishable_key
     STRIPE_SECRET=your_stripe_secret_key

     # PayPal
     PAYPAL_CLIENT_ID=your_paypal_client_id
     PAYPAL_SECRET=your_paypal_secret

     # Razorpay
     RAZORPAY_KEY=your_razorpay_key
     RAZORPAY_SECRET=your_razorpay_secret
     ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Serve the application**
   ```bash
   php artisan serve
   ```

---

## 📦 Usage

Example route for creating a checkout:
```php
$checkout = app(\PaymentIntegrator\PaymentGatewayManager::class)
    ->createCheckout(1999, 'USD', 'Pro Plan', ['order_id' => '123']);
return response()->json($checkout);
```

**Webhooks** will be available at:
```
POST /payments/webhook/{driver}   # e.g., /payments/webhook/stripe
```

---

## 📄 License
This project is open-source and available under the [MIT License](LICENSE).
