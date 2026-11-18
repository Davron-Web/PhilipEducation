<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Пересчитывает users.points из xp_events — на случай, если денормализованная
 * сумма разъехалась с журналом (ручная правка в БД, сбой на середине
 * транзакции и т.п.). XpService::award() пишет обе стороны атомарно, поэтому
 * в норме команда ничего не меняет — это восстановление, а не обычный шаг.
 */
class RecalculatePoints extends Command
{
    protected $signature = 'points:recalculate';

    protected $description = 'Пересчитать users.points из xp_events';

    public function handle(): int
    {
        $sums = DB::table('xp_events')
            ->select('user_id', DB::raw('sum(amount) as total'))
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $changed = 0;

        User::query()->select('id', 'points')->chunkById(500, function ($users) use ($sums, &$changed) {
            foreach ($users as $user) {
                $correct = (int) ($sums[$user->id] ?? 0);

                if ($user->points !== $correct) {
                    $user->forceFill(['points' => $correct])->save();
                    $changed++;
                }
            }
        });

        $this->info("Проверено пользователей: {$sums->count()} с очками в журнале. Исправлено расхождений: {$changed}.");

        return self::SUCCESS;
    }
}
