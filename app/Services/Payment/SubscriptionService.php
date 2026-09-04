<?php

namespace App\Services\Payment;

use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use App\Models\Billing\Plan;
use App\Models\Billing\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Превращает оплату в доступ. Вся логика продления собрана здесь, чтобы
 * контроллер и вебхук не расходились в трактовке сроков.
 */
class SubscriptionService
{
    public function __construct(private readonly PaymentGateway $gateway) {}

    /**
     * Создаёт платёж в статусе pending и возвращает его вместе со ссылкой
     * на страницу оплаты провайдера.
     *
     * @return array{payment: Payment, checkout_url: string}
     */
    public function startCheckout(User $user, Plan $plan): array
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount_minor' => $plan->price_minor,
            'currency' => $plan->currency,
            'status' => Payment::STATUS_PENDING,
            'provider' => $this->gateway->name(),
            'reference' => 'pe-'.Str::lower(Str::random(20)),
        ]);

        return [
            'payment' => $payment,
            'checkout_url' => $this->gateway->createCheckout($payment),
        ];
    }

    /**
     * Отмечает платёж оплаченным и выдаёт/продлевает подписку.
     *
     * Идемпотентен: провайдеры регулярно шлют один и тот же вебхук
     * повторно, и без этой проверки пользователь получил бы два периода
     * за одну оплату.
     */
    public function markPaid(Payment $payment, ?string $providerPaymentId = null, array $payload = []): Subscription
    {
        return DB::transaction(function () use ($payment, $providerPaymentId, $payload) {
            $payment->refresh();

            if ($payment->status === Payment::STATUS_PAID && $payment->subscription_id) {
                return $payment->subscription;
            }

            $subscription = $this->extendOrCreate($payment->user, $payment->plan);

            $payment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'provider_payment_id' => $providerPaymentId ?? $payment->provider_payment_id,
                'payload' => $payload ?: $payment->payload,
                'subscription_id' => $subscription->id,
            ]);

            $this->issueInvoice($payment->refresh(), $subscription);

            return $subscription;
        });
    }

    /**
     * Выписывает счёт по оплаченному платежу.
     *
     * firstOrCreate по payment_id: вебхук может прийти повторно, и второй
     * счёт за ту же оплату сломал бы отчётность по доходу.
     */
    private function issueInvoice(Payment $payment, Subscription $subscription): Invoice
    {
        return Invoice::firstOrCreate(
            ['payment_id' => $payment->id],
            [
                'number' => Invoice::numberFor($payment),
                'user_id' => $payment->user_id,
                'subscription_id' => $subscription->id,
                'plan_name' => $payment->plan?->name ?? 'Подписка',
                'amount_minor' => $payment->amount_minor,
                'currency' => $payment->currency,
                'status' => Invoice::STATUS_PAID,
                'issued_at' => $payment->paid_at ?? now(),
            ]
        );
    }

    public function markFailed(Payment $payment, array $payload = []): void
    {
        if ($payment->status === Payment::STATUS_PAID) {
            return; // Оплаченный платёж не «разоплачиваем» задним числом.
        }

        $payment->update([
            'status' => Payment::STATUS_FAILED,
            'payload' => $payload ?: $payment->payload,
        ]);
    }

    /**
     * Продлевает действующую подписку от её даты окончания, а не от
     * сегодняшнего дня — иначе оплатив заранее, пользователь терял бы
     * остаток уже оплаченного периода.
     */
    private function extendOrCreate(User $user, Plan $plan): Subscription
    {
        $current = $user->activeSubscription();

        // Бессрочный доступ продлевать нечем — оставляем как есть.
        // Оплата всё равно зафиксирована в payments.
        if ($current && $current->isLifetime()) {
            return $current;
        }

        if ($current) {
            $current->update([
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'cancelled_at' => null,
                'ends_at' => $current->ends_at->copy()->addDays($plan->duration_days),
            ]);

            return $current;
        }

        return $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
        ]);
    }

    /**
     * Отмена — это отказ от продления, а не мгновенное отключение:
     * доступ сохраняется до конца оплаченного периода.
     */
    public function cancel(Subscription $subscription): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Премиум в подарок от администратора — без оплаты.
     *
     * @param  int|null  $days  срок в днях; null — бессрочно
     */
    public function grant(User $user, ?int $days, ?User $grantedBy = null, ?string $note = null): Subscription
    {
        $current = $user->activeSubscription();

        // Уже есть доступ — продлеваем его, а не заводим второй,
        // иначе activeSubscription() выбирал бы из нескольких.
        if ($current) {
            $current->update([
                'status' => Subscription::STATUS_ACTIVE,
                'cancelled_at' => null,
                'source' => Subscription::SOURCE_GIFT,
                'granted_by' => $grantedBy?->id,
                'note' => $note,
                'ends_at' => $days === null
                    ? null
                    : ($current->ends_at ?? now())->copy()->addDays($days),
            ]);

            return $current;
        }

        return $user->subscriptions()->create([
            'plan_id' => null,
            'status' => Subscription::STATUS_ACTIVE,
            'source' => Subscription::SOURCE_GIFT,
            'granted_by' => $grantedBy?->id,
            'note' => $note,
            'starts_at' => now(),
            'ends_at' => $days === null ? null : now()->addDays($days),
        ]);
    }

    /**
     * Отзыв подарка — в отличие от отмены, закрывает доступ сразу:
     * денег за него не платили.
     */
    public function revoke(Subscription $subscription): void
    {
        $subscription->update([
            'status' => Subscription::STATUS_EXPIRED,
            'ends_at' => now(),
            'cancelled_at' => now(),
        ]);
    }
}
