<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\StudyReminder;
use Illuminate\Console\Command;

/**
 * Напоминает вернуться к занятиям тем, кто пропал на неделю.
 *
 * Повторно не беспокоим: если такое напоминание уже уходило за последние
 * 14 дней, пропускаем — иначе оно превратилось бы в еженедельный спам
 * для всех, кто ушёл насовсем.
 */
class SendStudyReminders extends Command
{
    protected $signature = 'students:remind {--days=7 : сколько дней отсутствия считать поводом}';

    protected $description = 'Напомнить об учёбе тем, кто давно не заходил';

    private const REMINDER_TITLE = 'Пора вернуться к занятиям';

    private const COOLDOWN_DAYS = 14;

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $threshold = now()->subDays($days);
        $sent = 0;

        User::whereNotNull('last_login_at')
            ->where('last_login_at', '<', $threshold)
            ->whereDoesntHave('notifications', fn ($q) => $q
                ->where('title', self::REMINDER_TITLE)
                ->where('created_at', '>=', now()->subDays(self::COOLDOWN_DAYS)))
            ->chunkById(200, function ($users) use (&$sent) {
                foreach ($users as $user) {
                    $user->notify(new StudyReminder((int) $user->last_login_at->diffInDays(now())));
                    $sent++;
                }
            });

        $this->info("Напоминаний отправлено: {$sent}");

        return self::SUCCESS;
    }
}
