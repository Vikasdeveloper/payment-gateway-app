<?php

namespace PaymentIntegrator\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentGatewayManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'payment-integrator';
    }
}
