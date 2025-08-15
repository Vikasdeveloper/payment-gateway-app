<?php

namespace PaymentIntegrator;

use Illuminate\Support\ServiceProvider;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/payment-gateways.php', 'payment-gateways');

        $this->app->singleton('payment-integrator', function ($app) {
            return new PaymentGatewayManager($app);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payment-gateways.php' => config_path('payment-gateways.php'),
        ], 'config');

        if (function_exists('base_path')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }
}
