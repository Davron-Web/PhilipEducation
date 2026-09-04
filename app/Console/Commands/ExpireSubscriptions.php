<?php

namespace App\Console\Commands;

use App\Models\Billing\Subscription;
use Illuminate\Console\Command;

/**
 * Переводит истёкшие подписки в статус expired.
 *
 * Доступ и без этого определяется по ends_at, поэтому команда ничего не
 * «закрывает» — она приводит статус в соответствие с фактом. Это нужно
 * отчётам и админке: без неё запрос «сколько активных подписок» считал бы
 * давно закончившиеся, и в списке они выглядели бы действующими.
 */
class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Отметить истёкшие подписки как expired';

    public function handle(): int
    {
        $expired = Subscription::whereIn('status', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_CANCELLED,
            ])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->update(['status' => Subscription::STATUS_EXPIRED]);

        $this->info("Истёкших подписок отмечено: {$expired}");

        return self::SUCCESS;
    }
}
