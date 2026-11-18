<?php

namespace App\Services;

/**
 * Диалог «Спросить Phil» (phil-widget.blade.php) — определение языка и
 * учебное исключение (английские слова/примеры остаются на английском)
 * живут в системном промпте (config/phil.php, ключ assistant.system), не
 * в коде: формулировки можно поправить без правки PHP.
 */
class PhilAssistantService
{
    private const LOCALE_NAMES = [
        'ru' => 'русском',
        'en' => 'английском',
        'tg' => 'таджикском',
    ];

    public function __construct(private readonly GeminiService $gemini) {}

    /**
     * @param  array<int, array{role?: string, text?: string}>  $history  Реплики диалога ДО текущего вопроса, старые→новые.
     * @param  string  $fallbackLocale  Язык интерфейса пользователя (session('locale') / App::getLocale()) — используется,
     *                                  только если язык самого сообщения определить не удалось и в истории не за что зацепиться.
     */
    public function reply(array $history, string $question, ?string $context, string $fallbackLocale): string
    {
        $system = $this->buildSystemPrompt($fallbackLocale, $context);

        $limit = (int) config('phil.assistant.history_limit', 6);
        $turns = array_merge(
            array_slice($this->sanitizeHistory($history), -$limit),
            [['role' => 'user', 'text' => $question]],
        );

        return $this->gemini->converse($turns, $system);
    }

    private function buildSystemPrompt(string $fallbackLocale, ?string $context): string
    {
        $localeName = self::LOCALE_NAMES[$fallbackLocale] ?? self::LOCALE_NAMES[config('phil.assistant.fallback_locale', 'ru')];

        $system = str_replace(
            '{fallback_locale_name}',
            $localeName,
            config('phil.assistant.system'),
        );

        if ($context) {
            // Тот же приём, что раньше был в GeminiService::answerQuestion():
            // контекст страницы (слово/выражение) добавляется инструкцией
            // поверх основного промпта, а не как отдельная реплика диалога.
            $system .= " Сейчас пользователь смотрит страницу: {$context}. Если его вопрос касается этого слова или выражения"
                .' (например «объясни», «приведи пример», «как использовать», «переведи»), отвечай именно про него, не переспрашивая, что он имеет в виду.';
        }

        return $system;
    }

    /**
     * Оставляет только валидные реплики (role — user/bot, есть непустой text) —
     * фронт шлёт то, что сам же и хранил в sessionStorage, но на вход
     * контроллера это всё равно недоверенные данные пользователя.
     *
     * @return array<int, array{role: string, text: string}>
     */
    private function sanitizeHistory(array $history): array
    {
        return collect($history)
            ->filter(fn ($turn) => is_array($turn)
                && in_array($turn['role'] ?? null, ['user', 'bot'], true)
                && filled($turn['text'] ?? null))
            ->map(fn ($turn) => ['role' => $turn['role'], 'text' => (string) $turn['text']])
            ->values()
            ->all();
    }
}
