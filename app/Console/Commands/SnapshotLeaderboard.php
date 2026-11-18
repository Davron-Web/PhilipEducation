<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ежедневный снимок мест в рейтинге — без него не с чем сравнить текущее
 * место, чтобы показать стрелку изменения на странице лидеров (см.
 * LeaderboardService::changeFor()/previousRanks()). Идемпотентна (upsert по
 * user_id+period+snapshot_date), поэтому безопасно запускать вручную поверх
 * уже существующего снимка того же дня — например, для первого бэкфилла.
 */
class SnapshotLeaderboard extends Command
{
    protected $signature = 'leaderboard:snapshot';

    protected $description = 'Сохранить текущие места всех пользователей в рейтинге за все периоды';

    public function handle(LeaderboardService $leaderboard): int
    {
        $today = now()->toDateString();
        $now = now();
        $total = 0;

        foreach (LeaderboardService::PERIODS as $period) {
            $rows = $leaderboard->fullRanking($period)->map(fn (array $row) => [
                'user_id' => $row['user_id'],
                'period' => $period,
                'rank' => $row['rank'],
                'points' => $row['points'],
                'snapshot_date' => $today,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($rows->chunk(500) as $chunk) {
                DB::table('leaderboard_snapshots')->upsert(
                    $chunk->all(),
                    ['user_id', 'period', 'snapshot_date'],
                    ['rank', 'points', 'updated_at']
                );
            }

            $this->info("{$period}: сохранено мест — {$rows->count()}.");
            $total += $rows->count();
        }

        $this->info("Снимок рейтинга на {$today} готов, всего записей: {$total}.");

        return self::SUCCESS;
    }
}
