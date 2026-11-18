<?php

namespace App\Services;

use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\Vocabulary\Word;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Дневное приветствие Phil на дашборде — на английском, под уровень
 * ученика, с живыми фактами о его реальном прогрессе. Промпты и фолбэк-
 * фразы — в config/phil.php, не здесь, чтобы текст можно было
 * поправить без правки кода.
 */
class PhilGreetingService
{
    public function __construct(private readonly GeminiService $gemini) {}

    /**
     * Кэш на сутки на пользователя — ключ включает дату, поэтому TTL
     * addDay() и естественная смена ключа на следующий день никогда не
     * расходятся между собой.
     *
     * Возвращаем только массив строк/скаляров, не объекты: в этом проекте
     * config('cache.serializable_classes') = false, и unserialize() любого
     * объекта из кэша превращается в __PHP_Incomplete_Class (см. тот же
     * приём в LeaderboardService).
     *
     * @return array{lines: array<int, array{text: string}>, html: string, translation: ?string}
     */
    public function greetingFor(User $user, ?Lesson $nextLesson): array
    {
        $cacheKey = "phil-greeting:{$user->id}:".now()->toDateString();

        return Cache::remember($cacheKey, now()->addDay(), fn () => $this->build($user, $nextLesson));
    }

    private function build(User $user, ?Lesson $nextLesson): array
    {
        $ctx = $this->context($user, $nextLesson);

        $pair = $this->generate($ctx) ?? $this->fallback($ctx);

        $maxChars = (int) config('phil.greeting.max_chars', 320);
        $en = Str::limit(trim($pair['en']), $maxChars, '…');
        $ru = trim($pair['ru'] ?? '');

        return [
            'lines' => $this->splitSentences($en),
            'html' => $this->annotate($en, $ctx['difficultyThreshold']),
            'translation' => $ru !== '' ? Str::limit($ru, $maxChars, '…') : null,
        ];
    }

    /**
     * @return array{
     *     firstName: string, levelCode: string, difficultyThreshold: int,
     *     scenario: string, streak: int, daysAway: ?int,
     *     lessonsCompleted: int, wordsLearned: int, nextLessonTitle: ?string
     * }
     */
    private function context(User $user, ?Lesson $nextLesson): array
    {
        $levelCode = $user->level->code ?? 'A2';

        $daysAway = $user->previous_login_at?->diffInDays(now());
        $isFirstLoginEver = $user->previous_login_at === null;
        $streak = $user->currentStreak();

        $scenario = match (true) {
            $isFirstLoginEver => 'new',
            $daysAway !== null && $daysAway >= 30 => 'returning_long',
            $daysAway !== null && $daysAway >= 3 => 'returning_short',
            $streak >= 2 => 'streak',
            default => 'default',
        };

        return [
            'firstName' => explode(' ', trim($user->name))[0] ?: $user->name,
            'levelCode' => $levelCode,
            'difficultyThreshold' => $this->difficultyForLevel($levelCode),
            'scenario' => $scenario,
            'streak' => $streak,
            'daysAway' => $daysAway,
            'lessonsCompleted' => $user->completedLessonsCount(),
            'wordsLearned' => $user->words()->wherePivot('learned', true)->count(),
            'nextLessonTitle' => $nextLesson->title ?? null,
        ];
    }

    /**
     * Тот же порог, что и Admin\AiController::difficultyForLevel() при
     * генерации уроков — держим "что считается сложным" в одну логику
     * с тем, что уже подбирается по уровню в остальном приложении.
     */
    private function difficultyForLevel(string $levelCode): int
    {
        return match (true) {
            str_contains($levelCode, 'A1') => 2,
            str_contains($levelCode, 'A2') => 4,
            str_contains($levelCode, 'B1') => 6,
            str_contains($levelCode, 'B2') => 7,
            str_contains($levelCode, 'C1'), str_contains($levelCode, 'C2') => 9,
            default => 4,
        };
    }

    /** @return array{en: string, ru: string}|null */
    private function generate(array $ctx): ?array
    {
        try {
            $system = config('phil.greeting.system');
            $levelInstruction = config("phil.greeting.levels.{$ctx['levelCode']}") ?? config('phil.greeting.levels.A2');
            $scenarioInstruction = config("phil.greeting.scenarios.{$ctx['scenario']}");
            $maxTokens = (int) config('phil.greeting.max_output_tokens', 400);

            $prompt = $this->buildPrompt($ctx, $levelInstruction, $scenarioInstruction);

            $raw = trim($this->gemini->ask($prompt, $system, json: true, maxOutputTokens: $maxTokens));
            $raw = preg_replace(['/^```(json)?/u', '/```$/u'], '', $raw);

            $data = json_decode($raw, true);

            if (! is_array($data) || empty($data['en'])) {
                return null;
            }

            return ['en' => (string) $data['en'], 'ru' => (string) ($data['ru'] ?? '')];
        } catch (\Throwable $e) {
            // API недоступен/квота исчерпана/что угодно ещё — баннер не
            // должен остаться пустым или показать ошибку, поэтому здесь
            // молча уходим в fallback(), а не пробрасываем исключение выше.
            report($e);

            return null;
        }
    }

    private function buildPrompt(array $ctx, string $levelInstruction, string $scenarioInstruction): string
    {
        $facts = ["Student's first name: {$ctx['firstName']}"];

        if ($ctx['scenario'] === 'new') {
            $facts[] = 'This is their very first visit to the platform — they have not completed any lessons yet.';
        } else {
            $facts[] = "Current streak: {$ctx['streak']} day(s) in a row.";
            if ($ctx['daysAway'] !== null) {
                $facts[] = "Days since their last visit before today: {$ctx['daysAway']}.";
            }
            $facts[] = "Lessons completed so far: {$ctx['lessonsCompleted']}.";
            $facts[] = "Words learned so far: {$ctx['wordsLearned']}.";
        }

        $facts[] = $ctx['nextLessonTitle']
            ? "Next lesson title: \"{$ctx['nextLessonTitle']}\"."
            : 'They have completed every published lesson available right now.';

        return "Write today's dashboard greeting.\n\n".
            "Level: {$ctx['levelCode']}. {$levelInstruction}\n\n".
            "Situation: {$scenarioInstruction}\n\n".
            "Facts you must weave in naturally (do not just list them one by one):\n- ".implode("\n- ", $facts)."\n\n".
            'Return STRICT JSON, no markdown fences: {"en": "the greeting in English", "ru": "a faithful Russian translation of that exact same greeting"}.';
    }

    /** @return array{en: string, ru: string} */
    private function fallback(array $ctx): array
    {
        $templates = config('phil.greeting.fallback', []);
        $pick = $templates[array_rand($templates)] ?? [
            'en' => 'Welcome back, {name}! Ready for {lesson}?',
            'ru' => 'С возвращением, {name}! Готовы к «{lesson}»?',
        ];

        $lesson = $ctx['nextLessonTitle'] ?? "today's practice";

        return [
            'en' => strtr($pick['en'], ['{name}' => $ctx['firstName'], '{lesson}' => $lesson]),
            'ru' => strtr($pick['ru'], ['{name}' => $ctx['firstName'], '{lesson}' => $lesson]),
        ];
    }

    /** @return array<int, array{text: string}> */
    private function splitSentences(string $text): array
    {
        $parts = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return array_map(fn ($t) => ['text' => $t], $parts ?: [$text]);
    }

    /**
     * Оборачивает в <span> с подсказкой слова, которые одновременно:
     * (а) сложны для уровня ученика (Word.difficulty >= порога уровня),
     * (б) есть в словаре — это единственный источник перевода и
     * транскрипции, придумывать их самим мы не станем (п.5 ТЗ: "если
     * слово там есть").
     */
    private function annotate(string $text, int $threshold): string
    {
        $tokens = preg_split('/([A-Za-z\']+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($tokens === false) {
            return e($text);
        }

        $uniqueWords = collect($tokens)
            ->filter(fn ($chunk, $i) => $i % 2 === 1 && $chunk !== '')
            ->map(fn ($w) => mb_strtolower($w))
            ->unique()
            ->values();

        $dictionary = collect();

        if ($uniqueWords->isNotEmpty()) {
            $placeholders = implode(',', array_fill(0, $uniqueWords->count(), '?'));

            $dictionary = Word::whereRaw("LOWER(word) IN ({$placeholders})", $uniqueWords->all())
                ->where('difficulty', '>=', $threshold)
                ->with(['translations' => fn ($q) => $q->where('language', 'ru')])
                ->get()
                ->keyBy(fn (Word $w) => mb_strtolower($w->word));
        }

        $html = '';

        foreach ($tokens as $i => $chunk) {
            if ($chunk === '') {
                continue;
            }

            // Чётные куски — то, что осталось МЕЖДУ словами (пробелы,
            // пунктуация): выводим как есть, только экранированными.
            if ($i % 2 === 0) {
                $html .= e($chunk);

                continue;
            }

            $word = $dictionary->get(mb_strtolower($chunk));

            if (! $word) {
                $html .= e($chunk);

                continue;
            }

            $translation = $word->translations->first()?->translation;
            $tooltip = trim(($word->transcription ? "/{$word->transcription}/ " : '').($translation ?? ''));

            $html .= $tooltip === ''
                ? e($chunk)
                : '<span class="phil-word" tabindex="0" title="'.e($tooltip).'">'.e($chunk).'</span>';
        }

        return $html;
    }
}
