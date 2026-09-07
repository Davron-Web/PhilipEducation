<?php

namespace App\Console\Commands\Users;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Удаляет сгенерированные учётные записи вместе со всем, что к ним привязано.
 *
 * Такие аккаунты создаёт FakeUsersSeeder — адреса вида
 * «имя.фамилия7@example.com». Пока они в базе, статистика платформы —
 * доход, активность, средний балл — считается по выдуманным людям.
 *
 * По умолчанию команда ничего не удаляет: показывает, что будет затронуто,
 * и требует --force. Настоящие пользователи (не на указанном домене) не
 * трогаются вовсе; отдельно команда отказывается удалять администраторов и
 * преподавателей, даже если их адрес попал под маску.
 */
class PurgeFakeUsers extends Command
{
    protected $signature = 'users:purge-fake
        {--domain=example.com : домен, аккаунты на котором считаются сгенерированными}
        {--force : выполнить удаление; без этого флага команда только показывает план}';

    protected $description = 'Удалить сгенерированные аккаунты и связанные с ними данные';

    /**
     * Таблицы, которые нужно чистить вручную, в порядке удаления.
     *
     * У 11 из них внешний ключ объявлен как NO ACTION: база не удалит эти
     * строки сама и не даст удалить пользователя, пока они есть. Остальные
     * (xp_events, user_titles, test_drafts и прочие с CASCADE) уходят сами,
     * но перечислены не все — только те, без которых удаление упадёт.
     */
    private const TABLES = [
        // Сначала то, что ссылается на попытки, — иначе не удалить сами попытки.
        'user_answers' => ['via' => 'test_attempts', 'key' => 'attempt_id'],

        'test_attempts' => ['key' => 'user_id'],
        'user_results' => ['key' => 'user_id'],
        'user_progress' => ['key' => 'user_id'],
        'user_words' => ['key' => 'user_id'],
        'user_expressions' => ['key' => 'user_id'],
        'user_achievements' => ['key' => 'user_id'],
        'study_statistics' => ['key' => 'user_id'],
        'notifications' => ['key' => 'user_id'],
        'certificates' => ['key' => 'user_id'],
        'lesson_comments' => ['key' => 'user_id'],
        'favorites' => ['key' => 'user_id'],
    ];

    public function handle(): int
    {
        $domain = ltrim((string) $this->option('domain'), '@');

        $targets = User::where('email', 'like', '%@'.$domain)->pluck('id');

        if ($targets->isEmpty()) {
            $this->info("Аккаунтов на домене {$domain} не найдено — удалять нечего.");

            return self::SUCCESS;
        }

        // Защита от опечатки в --domain: под маску не должны попадать
        // сотрудники, даже если их адрес на этом домене.
        $staff = User::whereIn('id', $targets)
            ->whereHas('role', fn ($q) => $q->whereIn('name', ['admin', 'teacher']))
            ->get(['id', 'name', 'email']);

        if ($staff->isNotEmpty()) {
            $this->error('Под маску попали администраторы или преподаватели — команда остановлена:');
            $staff->each(fn ($u) => $this->line("  #{$u->id}  {$u->email}"));
            $this->line('Уточните --domain или смените адрес этим учётным записям.');

            return self::FAILURE;
        }

        $this->showPreserved($domain);
        $counts = $this->countRelated($targets);

        $this->newLine();
        $this->line("Будет удалено аккаунтов: <options=bold>{$targets->count()}</> (домен {$domain})");
        $this->table(['Таблица', 'Строк'], $counts->map(fn ($n, $t) => [$t, $n])->values()->all());

        if (! $this->option('force')) {
            $this->newLine();
            $this->warn('Ничего не удалено. Чтобы выполнить: php artisan users:purge-fake --force');

            return self::SUCCESS;
        }

        $this->newLine();
        $deleted = $this->purge($targets);

        $this->info("Удалено аккаунтов: {$deleted}.");
        $this->line('Осталось пользователей: '.User::count().'.');

        return self::SUCCESS;
    }

    /** Показывает, кто останется, — чтобы ошибку было видно до удаления. */
    private function showPreserved(string $domain): void
    {
        $preserved = User::where('email', 'not like', '%@'.$domain)
            ->with('role')
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'role_id', 'created_at']);

        $this->line('<options=bold>Эти аккаунты останутся нетронутыми:</>');

        if ($preserved->isEmpty()) {
            $this->error('  ни одного — почти наверняка ошибка в --domain');

            return;
        }

        $this->table(
            ['ID', 'Имя', 'Email', 'Роль', 'Создан'],
            $preserved->map(fn (User $u) => [
                $u->id,
                $u->name,
                $u->email,
                $u->role?->name ?? '—',
                $u->created_at?->format('d.m.Y') ?? '—',
            ])->all()
        );
    }

    /** @return Collection<string, int> */
    private function countRelated(Collection $userIds): Collection
    {
        $counts = collect(['users' => $userIds->count()]);

        foreach (self::TABLES as $table => $config) {
            $counts[$table] = $this->relatedQuery($table, $config, $userIds)->count();
        }

        return $counts->filter(fn ($n) => $n > 0);
    }

    private function relatedQuery(string $table, array $config, Collection $userIds)
    {
        if (isset($config['via'])) {
            return DB::table($table)->whereIn($config['key'], fn ($q) => $q
                ->select('id')->from($config['via'])->whereIn('user_id', $userIds));
        }

        return DB::table($table)->whereIn($config['key'], $userIds);
    }

    private function purge(Collection $userIds): int
    {
        $deleted = 0;

        // Порциями и каждая в своей транзакции: полторы тысячи аккаунтов с
        // их данными — это сотни тысяч строк, и одна транзакция на всё
        // держала бы таблицы заблокированными надолго.
        $userIds->chunk(100)->each(function (Collection $chunk) use (&$deleted) {
            DB::transaction(function () use ($chunk, &$deleted) {
                foreach (self::TABLES as $table => $config) {
                    $this->relatedQuery($table, $config, $chunk)->delete();
                }

                $deleted += User::whereIn('id', $chunk)->delete();
            });

            $this->line("  удалено: {$deleted}");
        });

        return $deleted;
    }
}
