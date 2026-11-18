<?php

namespace App\Console\Commands;

use App\Models\Chat\ChatMessage;
use App\Services\ChatBotService;
use Illuminate\Console\Command;

/**
 * Раз в 2 минуты (routes/console.php) один бот пишет реплику в общий чат —
 * строго на СВОЁМ языке (у каждого бота users.lang закреплён навсегда,
 * см. UserFactory::bot()). Разговор внутри одного языка ведут только боты
 * этого языка; между разговорами — пауза (эта команда просто ничего не
 * пишет часть тиков), языки разговоров чередуются (ChatBotService::
 * nextConversationLanguage()). После публикации чат обрезается до
 * ChatBotService::MESSAGE_LIMIT последних сообщений.
 */
class PostBotChatMessages extends Command
{
    protected $signature = 'chat:bots';

    protected $description = 'Опубликовать одну реплику от бота в общий чат (или выдержать паузу между разговорами)';

    /** Шанс промолчать в этот тик, когда предыдущий разговор уже закончился — имитирует паузу между беседами. */
    private const PAUSE_CHANCE = 45;

    public function handle(ChatBotService $service): int
    {
        $last = $service->lastMessage();
        [$conversationActive, $lang] = $service->activeConversation($last);

        if ($conversationActive) {
            $bots = $service->botsForLanguage($lang)->filter(fn ($b) => $b->id !== $last->user_id);

            if ($bots->isEmpty()) {
                $this->warn("Нет второго бота языка «{$lang}» для ответа — пропускаю тик.");

                return self::SUCCESS;
            }

            $author = $bots->random();
            $category = $service->isQuestion($lang, $last->body)
                ? $service->randomAnswerCategory()
                : $service->randomCategory();
        } else {
            // Предыдущий разговор (если был) уже закончился — не начинаем
            // новый на каждом тике подряд, иначе паузы между беседами
            // пропадут и переписка будет литься непрерывным потоком.
            if ($last && fake()->boolean(self::PAUSE_CHANCE)) {
                $this->info('Пауза между разговорами — в этот раз ничего не пишем.');

                return self::SUCCESS;
            }

            $lang = $service->nextConversationLanguage();
            $bots = $service->botsForLanguage($lang);

            if ($bots->isEmpty()) {
                $this->warn("Ботов языка «{$lang}» нет — запустите php artisan db:seed --class=ChatBotSeeder.");

                return self::FAILURE;
            }

            $author = $bots->random();
            // Новый разговор естественнее начать с приветствия или вопроса,
            // а не сразу с реакции на то, чего ещё не было сказано.
            $category = fake()->randomElement(['greeting', ...config('chat_bots.question_categories')]);
        }

        $body = $service->randomPhrase($lang, $category);

        ChatMessage::create([
            'user_id' => $author->id,
            'body' => $body,
        ]);

        $service->pruneOldMessages();

        $this->info("{$author->name} ({$lang}): {$body}");

        return self::SUCCESS;
    }
}
