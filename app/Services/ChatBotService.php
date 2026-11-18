<?php

namespace App\Services;

use App\Models\Chat\ChatMessage;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Общая логика ботов в чате — используется и сидером истории
 * (ChatBotSeeder), и командой chat:bots, чтобы правило "один разговор —
 * один язык" не могло разъехаться между ними.
 *
 * "Разговор" здесь — не отдельная сущность в БД (в chat_messages нет
 * поля conversation_id), а понятие, которое каждый раз заново
 * восстанавливается из истории: смотрим на язык и время последнего
 * сообщения. Разговор считается идущим, пока последнее сообщение свежее
 * FOLLOW_UP_WINDOW_MINUTES и короче MAX_CONVERSATION_LENGTH реплик —
 * этого достаточно, чтобы chat:bots (без собственной памяти между
 * запусками, раз в 2 минуты) вело себя последовательно.
 */
class ChatBotService
{
    public const LANGUAGES = ['tg', 'ru'];

    /** Сколько минут с последнего сообщения считаем тем же разговором. */
    public const FOLLOW_UP_WINDOW_MINUTES = 8;

    /** После скольки реплик подряд разговор считается исчерпанным. */
    public const MAX_CONVERSATION_LENGTH = 6;

    /** Сколько сообщений держим в общем чате — старые сверх лимита удаляются. */
    public const MESSAGE_LIMIT = 500;

    /** Определяет язык реплики по пулу фраз — по нему решаем, кто может ответить. */
    public function detectLanguage(string $body): ?string
    {
        $phrases = config('chat_bots.phrases');

        foreach (self::LANGUAGES as $lang) {
            foreach ($phrases[$lang] as $variants) {
                if (in_array($body, $variants, true)) {
                    return $lang;
                }
            }
        }

        return null;
    }

    /**
     * Последнее (видимое) сообщение в чате — единая точка правды для всех
     * "что там в конце ленты" проверок ниже, чтобы не дублировать запрос.
     */
    public function lastMessage(): ?ChatMessage
    {
        return ChatMessage::visible()->latest('id')->first();
    }

    /**
     * Идёт ли сейчас разговор, и если да — на каком языке. Null вторым
     * элементом — разговора нет (либо последнее сообщение слишком старое,
     * либо его язык не из пула, либо реплик уже накопилось слишком много).
     *
     * @return array{0: bool, 1: ?string}
     */
    public function activeConversation(?ChatMessage $last = null): array
    {
        $last ??= $this->lastMessage();

        if (! $last || $last->created_at->diffInMinutes(now()) > self::FOLLOW_UP_WINDOW_MINUTES) {
            return [false, null];
        }

        $lang = $this->detectLanguage($last->body);

        if (! $lang || $this->conversationLength($lang) >= self::MAX_CONVERSATION_LENGTH) {
            return [false, null];
        }

        return [true, $lang];
    }

    /** Сколько сообщений этого языка подряд написано в пределах окна разговора. */
    public function conversationLength(string $lang): int
    {
        $botIds = User::where('is_bot', true)->where('lang', $lang)->pluck('id');

        if ($botIds->isEmpty()) {
            return 0;
        }

        $recent = ChatMessage::visible()
            ->whereIn('user_id', $botIds)
            ->where('created_at', '>=', now()->subMinutes(self::FOLLOW_UP_WINDOW_MINUTES))
            ->orderByDesc('id')
            ->pluck('body');

        // Считаем подряд от конца, пока реплики действительно из пула этого
        // языка — случайное человеческое сообщение того же времени, если
        // оно затесалось между ботами, обрывает подсчёт, и это правильно:
        // разговор ботов таким сообщением прерван.
        $count = 0;
        foreach ($recent as $body) {
            if ($this->detectLanguage($body) !== $lang) {
                break;
            }
            $count++;
        }

        return $count;
    }

    /**
     * Язык для НОВОГО разговора — чередуем с предыдущим (а не берём
     * случайно каждый раз), чтобы со временем в чате были и таджикские,
     * и русские беседы, а не один язык на много раз подряд по чистой
     * случайности.
     */
    public function nextConversationLanguage(): string
    {
        $last = $this->lastMessage();
        $lastLang = $last ? $this->detectLanguage($last->body) : null;

        return match ($lastLang) {
            'tg' => 'ru',
            'ru' => 'tg',
            default => fake()->randomElement(self::LANGUAGES),
        };
    }

    /** @return Collection<int, User> */
    public function botsForLanguage(string $lang): Collection
    {
        return User::where('is_bot', true)->where('lang', $lang)->get();
    }

    public function randomPhrase(string $lang, string $category): string
    {
        return fake()->randomElement(config("chat_bots.phrases.{$lang}.{$category}"));
    }

    public function randomCategory(): string
    {
        return fake()->randomElement(array_keys(config('chat_bots.phrases.ru')));
    }

    public function isQuestion(string $lang, string $body): bool
    {
        foreach (config('chat_bots.question_categories') as $category) {
            if (in_array($body, config("chat_bots.phrases.{$lang}.{$category}"), true)) {
                return true;
            }
        }

        return false;
    }

    public function randomAnswerCategory(): string
    {
        return fake()->randomElement(config('chat_bots.answer_categories'));
    }

    /**
     * Держит общий чат в пределах лимита — удаляет самые старые сообщения
     * сверх него (обычное удаление, не is_deleted: это не модерация,
     * а ротация истории, восстанавливать нечего).
     */
    public function pruneOldMessages(int $limit = self::MESSAGE_LIMIT): void
    {
        $total = ChatMessage::count();

        if ($total <= $limit) {
            return;
        }

        $ids = ChatMessage::oldest('id')->limit($total - $limit)->pluck('id');
        ChatMessage::whereIn('id', $ids)->delete();
    }
}
