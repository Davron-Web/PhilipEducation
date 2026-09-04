<?php

namespace App\Console\Commands;

use App\Models\Billing\Subscription;
use App\Notifications\SubscriptionExpiring;
use Illuminate\Console\Command;

/**
 * Предупреждает о скором конце подписки за 7 и за 1 день.
 *
 * Именно за фиксированные дни, а не «за неделю до»: команда идёт раз в
 * сутки, и диапазон дал бы семь писем подряд об одном и том же.
 */
class NotifyExpiringSubscriptions extends Command
{
    protected $signature = 'subscriptions:notify-expiring';

    protected $description = 'Напомнить о подписках, которые скоро закончатся';

    private const DAYS_BEFORE = [7, 1];

    public function handle(): int
    {
        $sent = 0;

        foreach (self::DAYS_BEFORE as $days) {
            $subscriptions = Subscription::with('user')
                ->where('status', Subscription::STATUS_ACTIVE)
                ->whereNotNull('ends_at')
                ->whereDate('ends_at', now()->addDays($days)->toDateString())
                ->get();

            foreach ($subscriptions as $subscription) {
                if (! $subscription->user) {
                    continue;
                }

                $subscription->user->notify(new SubscriptionExpiring($subscription, $days));
                $sent++;
            }
        }

        $this->info("Предупреждений отправлено: {$sent}");

        return self::SUCCESS;
    }
}
