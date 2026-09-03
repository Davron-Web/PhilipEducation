<?php

namespace App\Providers;

use App\Services\Payment\PaymentGateway;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, function ($app) {
            $name = config('payment.gateway');

            // Песочница подтверждает оплату без денег — на боевом сервере
            // это означало бы бесплатный доступ для любого желающего.
            if ($name === 'sandbox' && $app->environment('production')) {
                throw new RuntimeException(
                    'Платёжная песочница запрещена в production. Укажите реального провайдера в PAYMENT_GATEWAY.'
                );
            }

            $class = config("payment.gateways.{$name}");

            if (! $class || ! class_exists($class)) {
                throw new RuntimeException("Неизвестный платёжный провайдер: {$name}");
            }

            return $app->make($class);
        });
    }
}
