<?php

namespace Database\Seeders;

use App\Models\Chat\ChatMessage;
use App\Models\User;
use App\Services\ChatBotService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Наполняет общий чат правдоподобной историей от фейковых пользователей-
 * ботов — отдельными разговорами, каждый строго на одном языке (tg или
 * ru), с паузами между разговорами (config/chat_bots.php задаёт фразы,
 * ChatBotService — правила языка/пауз, общие с командой chat:bots).
 *
 * Идемпотентна: ботов не дублирует (добирает только до BOT_COUNT_PER_LANG
 * на каждый язык), историю добавляет только если сообщений от ботов ещё
 * нет — повторный запуск не плодит вторую копию.
 */
class ChatBotSeeder extends Seeder
{
    /** Ботов на каждый язык — итого вдвое больше, поровну tg/ru. */
    private const BOT_COUNT_PER_LANG = 6;

    private const HISTORY_MESSAGES = 180;

    private const HISTORY_DAYS = 14;

    public function run(): void
    {
        $tgBots = $this->ensureBots('tg');
        $ruBots = $this->ensureBots('ru');
        $allBotIds = $tgBots->pluck('id')->merge($ruBots->pluck('id'));

        if (ChatMessage::whereIn('user_id', $allBotIds)->exists()) {
            $this->command?->info('ChatBotSeeder: история от ботов уже есть, пропускаю.');

            return;
        }

        $this->seedHistory($tgBots, $ruBots);

        $this->command?->info(sprintf(
            'ChatBotSeeder: добавлено %d сообщений от %d ботов (%d tg + %d ru).',
            self::HISTORY_MESSAGES,
            $tgBots->count() + $ruBots->count(),
            $tgBots->count(),
            $ruBots->count(),
        ));
    }

    /** @return Collection<int, User> */
    private function ensureBots(string $lang): Collection
    {
        $existing = User::where('is_bot', true)->where('lang', $lang)->get();
        $missing = self::BOT_COUNT_PER_LANG - $existing->count();

        if ($missing > 0) {
            $existing = $existing->merge(User::factory()->count($missing)->bot($lang)->create());
        }

        return $existing;
    }

    private function seedHistory(Collection $tgBots, Collection $ruBots): void
    {
        $service = app(ChatBotService::class);
        $cursor = now()->subDays(self::HISTORY_DAYS);
        $end = now();
        $posted = 0;

        // $cursor растёт МОНОТОННО и никогда не отступает назад: более
        // ранняя версия могла "перескочить" курсор в случайную точку
        // прошлого при выходе за now(), из-за чего два соседних разговора
        // на разных языках могли получить пересекающиеся метки времени и
        // при сортировке по created_at перемешаться в один диалог. Тут
        // вместо отката просто останавливаемся, если время вышло —
        // 14 дней с лихвой хватает на 180 сообщений и без этого отката.
        while ($posted < self::HISTORY_MESSAGES && $cursor->isBefore($end)) {
            // Язык выбирается один раз на весь разговор и не меняется до
            // его конца — ровно то же правило, что и у live-команды.
            $lang = fake()->randomElement(ChatBotService::LANGUAGES);
            $bots = $lang === 'tg' ? $tgBots : $ruBots;

            $conversationLength = min(
                fake()->numberBetween(2, ChatBotService::MAX_CONVERSATION_LENGTH),
                self::HISTORY_MESSAGES - $posted,
            );

            $lastAuthorId = null;
            $lastBody = null;

            for ($i = 0; $i < $conversationLength && $cursor->isBefore($end); $i++) {
                $author = $bots->filter(fn (User $b) => $b->id !== $lastAuthorId)->random();

                $isFollowUp = $lastBody !== null && $service->isQuestion($lang, $lastBody) && fake()->boolean(70);
                $category = $isFollowUp ? $service->randomAnswerCategory() : $service->randomCategory();
                $body = $service->randomPhrase($lang, $category);

                // forceCreate, не create(): created_at/updated_at не входят
                // в $fillable (обычно их выставляет сама Eloquent), а тут
                // нужно задать их явно, чтобы история была растянута по
                // датам, а не вся с отметкой "только что".
                ChatMessage::forceCreate([
                    'user_id' => $author->id,
                    'body' => $body,
                    'created_at' => $cursor,
                    'updated_at' => $cursor,
                ]);

                $lastAuthorId = $author->id;
                $lastBody = $body;
                $posted++;

                // Внутри разговора — быстрый темп, реплика за репликой.
                $cursor = $cursor->copy()->addMinutes(fake()->numberBetween(1, 4));
            }

            // Пауза МЕЖДУ разговорами — заметно длиннее, чем между
            // репликами внутри одного: именно она делает историю похожей
            // на настоящую переписку, а не на равномерный поток сообщений.
            // Курсор только растёт — цикл сам остановится (условие while),
            // если время выйдет, без отката назад.
            $cursor = $cursor->copy()->addMinutes(fake()->numberBetween(20, 180));
        }
    }
}
