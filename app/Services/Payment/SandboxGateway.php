<?php

namespace App\Services\Payment;

use App\Models\Billing\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Тестовый «провайдер» для разработки: вместо банка показывает свою
 * страницу с кнопками «оплатить» и «отменить». Позволяет прогнать весь
 * цикл — создание платежа, оплата, вебхук, продление подписки — не имея
 * договора с банком.
 *
 * Никогда не должен быть включён на боевом сервере: он подтверждает
 * оплату без единого сомони. Защита — проверка в PaymentServiceProvider.
 */
class SandboxGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'sandbox';
    }

    public function createCheckout(Payment $payment): string
    {
        // Подписанная ссылка, чтобы чужой платёж нельзя было «оплатить»
        // простым подбором номера в адресной строке.
        return URL::signedRoute('billing.sandbox', ['payment' => $payment->reference]);
    }

    public function parseWebhook(Request $request): ?WebhookResult
    {
        $reference = $request->input('reference');
        if (! is_string($reference) || $reference === '') {
            return null;
        }

        return new WebhookResult(
            reference: $reference,
            paid: $request->boolean('paid'),
            providerPaymentId: 'sandbox-'.$reference,
            payload: $request->all(),
        );
    }
}
