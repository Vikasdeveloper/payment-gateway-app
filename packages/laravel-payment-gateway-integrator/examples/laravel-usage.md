# Example: Controller Usage in Laravel

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PaymentIntegrator\PaymentGatewayManager;

class CheckoutController extends Controller
{
    public function create(Request $request, PaymentGatewayManager $payments)
    {
        $amount = 4999; // $49.99
        $checkout = $payments->createCheckout($amount, 'USD', 'Pro Subscription', [
            'user_id' => $request->user()->id ?? null,
            'order_id' => 'ORD-' . now()->timestamp,
        ]);

        return response()->json($checkout);
    }
}
```
