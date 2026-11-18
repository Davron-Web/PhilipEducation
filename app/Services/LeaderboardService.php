<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Рейтинг строится поверх уже существующего экономического слоя XP
 * (users.points + xp_events, см. XpService) — отдельной таблицы очков для
 * рейтинга нет, чтобы не завести второй, расходящийся счётчик очков.
 *
 * Периоды week/month — скользящие окна (последние 7 / 30 дней), а не
 * календарная неделя/месяц.
 *
 * «Изменение места» — это НЕ сравнение текущего окна с предыдущим таким же
 * окном (так было раньше и почти всегда давало null: например, для 'month'
 * предыдущее окно — 30-60 дней назад, а столько истории в xp_events могло
 * ещё не накопиться). Вместо этого — сравнение текущего места с местом из
 * последнего ежедневного снимка (leaderboard_snapshots, команда
 * leaderboard:snapshot, см. previousRanks()/changeFor()): снимок существует
 * для всех, у кого вообще есть очки, независимо от возраста xp_events.
 */
class LeaderboardService
{
    public const PAGE_SIZE = 50;

    public const PERIODS = ['week', 'month', 'all'];

    /**
     * Топ-50 за период с местом и изменением места относительно последнего
     * снимка рейтинга. Результат живёт 10 минут — ключ включает период, поэтому
     * вкладки не путают кэш друг друга.
     *
     * Кэшируем только простой массив, не Collection/stdClass/Eloquent:
     * config('cache.serializable_classes') в этом проекте — false, а это
     * означает unserialize($v, ['allowed_classes' => false]) во всех
     * драйверах кэша (см. Illuminate\Cache\DatabaseStore::unserialize()) —
     * каждый объект при чтении из кэша превращается в
     * __PHP_Incomplete_Class. Не баг окружения и не то, что стоит обходить
     * ослаблением настройки: это осознанная защита от PHP object injection
     * на весь проект, подтверждено отдельной проверкой (Cache::remember()
     * с обычным array переживает round-trip, с любым объектом — нет).
     * Поэтому массив → объекты собираются здесь же, уже после кэша.
     *
     * @return Collection<int, object{rank: int, user: object, points: int, change: ?int}>
     */
    public function board(string $period): Collection
    {
        $rows = Cache::remember("leaderboard:{$period}", now()->addMinutes(10), function () use ($period) {
            $top = $this->scoreTable($period)
                ->orderByDesc('points')
                ->orderBy('last_at')
                ->limit(self::PAGE_SIZE)
                ->get();

            if ($top->isEmpty()) {
                return [];
            }

            $users = User::whereIn('id', $top->pluck('user_id'))
                ->get(['id', 'name', 'avatar'])
                ->keyBy('id');

            // Прошлые места всех кандидатов — одним запросом, а не по одному
            // на строку (иначе 50 строк = 50 лишних запросов на период).
            $previousRanks = $this->previousRanks($period);

            $rank = 0;
            $result = [];

            foreach ($top as $row) {
                $rank++;
                $user = $users->get($row->user_id);
                $prevRank = $previousRanks->get((int) $row->user_id);

                $result[] = [
                    'rank' => $rank,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar_url' => $user->avatar_url,
                        'initial' => $user->initial(),
                    ],
                    'points' => (int) $row->points,
                    'change' => $prevRank === null ? null : $prevRank - $rank,
                ];
            }

            return $result;
        });

        return collect($rows)->map(fn (array $row) => (object) [
            'rank' => $row['rank'],
            'user' => (object) $row['user'],
            'points' => $row['points'],
            'change' => $row['change'],
        ]);
    }

    /**
     * Место конкретного пользователя за период — через COUNT, не выгрузкой
     * всех пользователей в PHP. Null — у него ещё нет очков за этот период
     * (не участвует в рейтинге, а не «последнее место»).
     */
    public function rankFor(User $user, string $period): ?int
    {
        $row = $this->scoreTable($period)->where('user_id', $user->id)->first();

        if (! $row) {
            return null;
        }

        return 1 + $this->countBetter($period, (int) $row->points, $row->last_at);
    }

    /**
     * Плоский объект вместо Eloquent-модели — единый формат и для строк из
     * кэша (board()), и для закреплённой строки текущего пользователя,
     * чтобы x-leaderboard.row не знала о разнице между ними.
     */
    public function toRowUser(User $user): object
    {
        return (object) [
            'id' => $user->id,
            'name' => $user->name,
            'avatar_url' => $user->avatar_url,
            'initial' => $user->initial(),
        ];
    }

    public function pointsFor(User $user, string $period): int
    {
        $row = $this->scoreTable($period)->where('user_id', $user->id)->first();

        return $row ? (int) $row->points : 0;
    }

    /**
     * На сколько позиций пользователь поднялся (+) или опустился (-) со
     * вчерашнего снимка рейтинга (см. leaderboard:snapshot). Null — снимка
     * ещё нет (новый в рейтинге или снимок ни разу не запускался).
     *
     * Используется только для одиночной строки «моё место» — при отрисовке
     * топ-50 идёт через previousRanks(), чтобы не дёргать БД на каждую строку.
     */
    public function changeFor(int $userId, string $period, ?int $currentRank = null): ?int
    {
        $currentRank ??= $this->rankFor(User::query()->find($userId), $period);

        if ($currentRank === null) {
            return null;
        }

        $prevRank = DB::table('leaderboard_snapshots')
            ->where('period', $period)
            ->where('user_id', $userId)
            ->where('snapshot_date', '<', now()->toDateString())
            ->orderByDesc('snapshot_date')
            ->value('rank');

        return $prevRank === null ? null : $prevRank - $currentRank;
    }

    /**
     * Места из последнего снимка ДО сегодняшнего дня, одним запросом на весь
     * период — так колонка изменения на топ-50 не даёт N+1 (без этого на
     * каждую из 50 строк ушёл бы отдельный запрос за прошлым местом).
     *
     * @return Collection<int, int> место, индексировано по user_id
     */
    private function previousRanks(string $period): Collection
    {
        $latestDate = DB::table('leaderboard_snapshots')
            ->where('period', $period)
            ->where('snapshot_date', '<', now()->toDateString())
            ->max('snapshot_date');

        if (! $latestDate) {
            return collect();
        }

        return DB::table('leaderboard_snapshots')
            ->where('period', $period)
            ->where('snapshot_date', $latestDate)
            ->pluck('rank', 'user_id');
    }

    /**
     * Полный рейтинг за период (без лимита в 50) — для ежедневного снимка
     * (leaderboard:snapshot), которому нужны места всех участников, а не
     * только тех, кто виден на странице.
     *
     * @return Collection<int, array{user_id: int, rank: int, points: int}>
     */
    public function fullRanking(string $period): Collection
    {
        return $this->scoreTable($period)
            ->orderByDesc('points')
            ->orderBy('last_at')
            ->get(['user_id', 'points'])
            ->values()
            ->map(fn ($row, int $index) => [
                'user_id' => (int) $row->user_id,
                'rank' => $index + 1,
                'points' => (int) $row->points,
            ]);
    }

    /** Сколько человек за период набрали больше очков, либо столько же, но раньше (для подзапроса места). */
    private function countBetter(string $period, int $points, string $lastAt): int
    {
        return $this->scoreTable($period)
            ->where(fn ($q) => $q->where('points', '>', $points)
                ->orWhere(fn ($q2) => $q2->where('points', $points)->where('last_at', '<', $lastAt)))
            ->count();
    }

    /**
     * Производная таблица (user_id, points, last_at) для периода: очки за
     * окно и момент последнего начисления в этом окне — вторым служит для
     * сортировки «кто набрал раньше при равных очках выше» (п.5 ТЗ).
     *
     * Для 'all' берём users.points напрямую (уже denormalized и
     * поддерживается XpService::award() через increment() — ровно то, что
     * просили: не считать SUM на каждый запрос). Для недели/месяца сумма
     * считается из xp_events — точка отсчёта там скользящая, кэшированный
     * столбец этого не даст.
     */
    private function scoreTable(string $period): Builder
    {
        if ($period === 'all') {
            $sub = DB::table('users')
                ->select([
                    'id as user_id',
                    'points',
                    DB::raw('(select max(xe.created_at) from xp_events xe where xe.user_id = users.id) as last_at'),
                ])
                ->where('show_in_leaderboard', true)
                ->where('points', '>', 0);

            return DB::query()->fromSub($sub, 'scores');
        }

        [$from, $to] = $this->window($period);

        $sub = DB::table('xp_events')
            ->join('users', 'users.id', '=', 'xp_events.user_id')
            ->select([
                'xp_events.user_id',
                DB::raw('sum(xp_events.amount) as points'),
                DB::raw('max(xp_events.created_at) as last_at'),
            ])
            ->where('users.show_in_leaderboard', true)
            ->groupBy('xp_events.user_id')
            ->havingRaw('sum(xp_events.amount) > 0');

        if ($from) {
            $sub->where('xp_events.created_at', '>=', $from);
        }
        if ($to) {
            $sub->where('xp_events.created_at', '<=', $to);
        }

        return DB::query()->fromSub($sub, 'scores');
    }

    /** @return array{0: ?Carbon, 1: ?Carbon} */
    private function window(string $period): array
    {
        return match ($period) {
            'week' => [now()->subDays(7), null],
            'month' => [now()->subDays(30), null],
            default => [null, null],
        };
    }
}
