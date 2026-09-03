<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $key;

    protected string $model;

    protected string $url = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->key = config('services.gemini.key');
        $this->model = config('services.gemini.model');
    }

    /** Универсальный запрос к Gemini */
    public function ask(string $prompt, ?string $system = null, bool $json = false): string
    {
        $payload = [
            'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
        ];

        if ($system) {
            $payload['system_instruction'] = ['parts' => [['text' => $system]]];
        }
        if ($json) {
            $payload['generationConfig'] = [
                'response_mime_type' => 'application/json',
                'temperature' => 0.7,
            ];
        }

        return $this->generate($payload, 90)->json('candidates.0.content.parts.0.text', '');
    }

    /**
     * Один запрос к generateContent с повтором на временных ошибках.
     *
     * Gemini регулярно отвечает 503 «high demand» — без повтора это видит
     * пользователь: у него на ровном месте обрывается разговор с ботом.
     */
    private function generate(array $payload, int $timeout): \Illuminate\Http\Client\Response
    {
        $attempts = 3;

        for ($i = 1; ; $i++) {
            try {
                $res = Http::withHeaders(['x-goog-api-key' => $this->key])
                    ->timeout($timeout)
                    ->post("{$this->url}/{$this->model}:generateContent", $payload);
            } catch (ConnectionException $e) {
                // Таймаут или обрыв связи — повторяем, как и 5xx.
                if ($i >= $attempts) {
                    throw $e;
                }

                usleep(400_000 * $i);

                continue;
            }

            if ($res->successful()) {
                return $res;
            }

            // Повторяем только 5xx. 4xx запрос валиднее не сделает, а 429 —
            // это исчерпанная квота: быстрый повтор её только добивает.
            if (! $res->serverError() || $i >= $attempts) {
                throw new \RuntimeException('Gemini HTTP '.$res->status().': '.mb_substr($res->body(), 0, 300));
            }

            usleep(400_000 * $i);
        }
    }

    /** Генерация полного комплекта: урок + слова + упражнения + тест */
    public function generateLessonPack(string $topic): array
    {
        $prompt = <<<PROMPT
Создай учебный комплект по английскому языку на тему "{$topic}".
Верни СТРОГО JSON такой структуры:
{
  "lesson": {"title": "название урока", "level": "Beginner A1|Elementary A2|Intermediate B1", "theory": "объяснение темы на русском с примерами на английском"},
  "words": [{"word": "слово на английском", "translation": "перевод на русском"}],
  "exercises": [{"title": "название упражнения", "questions": [{"question": "задание", "answer": "правильный ответ"}]}],
  "test": {"title": "Тест: тема", "questions": [{"question": "вопрос", "options": ["вариант1","вариант2","вариант3","вариант4"], "correct": 0}]}
}
Требования: 6 слов, 2 упражнения по 3 вопроса, тест из 5 вопросов, "correct" — индекс правильного варианта (с 0).
PROMPT;

        $raw = trim($this->ask($prompt, json: true));
        $raw = preg_replace(['/^```(json)?/u', '/```$/u'], '', $raw);

        $data = json_decode($raw, true);
        if (! is_array($data)) {
            throw new \RuntimeException('Не удалось распарсить JSON от Gemini');
        }

        return $data;
    }

    /** Ответ на вопрос ученика */
    /**
     * $context — короткое текстовое описание слова/выражения, которое
     * пользователь сейчас просматривает (см. AssistantController::chat),
     * чтобы Phil мог отвечать по существу на «объясни это», «приведи пример»
     * и т.п. без необходимости пользователю копировать слово вручную.
     */
    public function answerQuestion(string $question, ?string $context = null): string
    {
        $system = 'Ты — дружелюбный преподаватель английского языка. Отвечай кратко и понятно на русском, с примерами на английском. Если вопрос не про английский — вежливо верни разговор к учёбе.';

        if ($context) {
            $system .= " Сейчас пользователь смотрит страницу: {$context}. Если его вопрос касается этого слова или выражения (например «объясни», «приведи пример», «как использовать», «переведи»), отвечай именно про него, не переспрашивая, что он имеет в виду.";
        }

        return $this->ask("Вопрос: {$question}", $system);
    }

    /**
     * Многоходовой диалог: в отличие от ask(), передаёт всю историю,
     * поэтому собеседник помнит, о чём шла речь.
     *
     * @param  array<int, array{role: string, text: string}>  $history
     */
    public function converse(array $history, string $system): string
    {
        $contents = [];

        foreach ($history as $turn) {
            $role = ($turn['role'] ?? 'user') === 'bot' ? 'model' : 'user';

            // Gemini требует, чтобы диалог начинался с реплики user, а наш бот
            // здоровается первым — отбрасываем ведущие реплики модели.
            if (! $contents && $role === 'model') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [['text' => (string) ($turn['text'] ?? '')]],
            ];
        }

        if (! $contents) {
            throw new \RuntimeException('История диалога не содержит реплик пользователя');
        }

        $payload = [
            'contents' => $contents,
            'system_instruction' => ['parts' => [['text' => $system]]],
            // Лимит щедрый: модели 2.5+ тратят часть бюджета на «размышления»,
            // и при 200 ответ обрывался на полуслове. Краткость держим промптом.
            'generationConfig' => ['temperature' => 0.9, 'maxOutputTokens' => 2000],
        ];

        $res = $this->generate($payload, 25);

        // Ответ иногда приходит несколькими частями — склеиваем, иначе
        // реплика обрывается на полуслове.
        $parts = $res->json('candidates.0.content.parts', []);

        return trim(implode('', array_column($parts, 'text')));
    }

    /**
     * Разбор разговорной практики: грамматика, выбор слов и слова, которые
     * распознаватель расслышал плохо — они чаще всего и есть проблемные
     * в произношении.
     *
     * @param  array<int, array{role: string, text: string}>  $history
     * @param  array<int, string>  $unclearWords  слова с низкой уверенностью распознавания
     * @return array{summary: string, mistakes: array<int, array{said: string, better: string, note: string}>, pronunciation: array<int, array{word: string, note: string}>}
     */
    public function reviewConversation(array $history, array $unclearWords): array
    {
        $transcript = collect($history)
            ->map(fn ($t) => (($t['role'] ?? 'user') === 'bot' ? 'Robot: ' : 'Student: ').($t['text'] ?? ''))
            ->implode("\n");

        $unclear = $unclearWords ? implode(', ', array_slice($unclearWords, 0, 30)) : '(нет)';

        $prompt = <<<PROMPT
Ты — преподаватель английского. Ниже расшифровка разговорной практики ученика с ботом.
Речь ученика распозналась автоматически, поэтому опечатки распознавания возможны.

Расшифровка:
{$transcript}

Слова, которые распознаватель расслышал неуверенно (вероятные проблемы с произношением):
{$unclear}

Верни СТРОГО JSON без markdown:
{
  "summary": "2-3 предложения на русском: общее впечатление от речи ученика и главное, над чем поработать",
  "mistakes": [
    {"said": "как сказал ученик", "better": "как правильно", "note": "коротко почему, на русском"}
  ],
  "pronunciation": [
    {"word": "слово", "note": "на что обратить внимание в произношении, на русском"}
  ]
}
В mistakes — до 5 реальных грамматических/лексических ошибок ученика (реплики бота не разбирай).
В pronunciation — до 5 слов из списка неуверенно распознанных; если список пуст, верни пустой массив.
Если ошибок нет, верни пустые массивы и похвали в summary.
PROMPT;

        $raw = $this->ask($prompt, 'Ты возвращаешь только валидный JSON.', true);
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return ['summary' => 'Не удалось разобрать разговор — попробуйте ещё раз.', 'mistakes' => [], 'pronunciation' => []];
        }

        return [
            'summary' => (string) ($data['summary'] ?? ''),
            'mistakes' => array_values(array_filter((array) ($data['mistakes'] ?? []), 'is_array')),
            'pronunciation' => array_values(array_filter((array) ($data['pronunciation'] ?? []), 'is_array')),
        ];
    }

    /**
     * Проверка письменного задания IELTS (Task 1 или Task 2) — экзаменатор
     * ставит band score 0-9 и даёт разбор по 4 критериям IELTS Writing.
     *
     * @return array{band: float, feedback: string}
     */
    public function gradeIeltsWriting(string $taskType, string $taskPrompt, string $answerText): array
    {
        $criteria = $taskType === 'writing_task1'
            ? 'Task Achievement, Coherence and Cohesion, Lexical Resource, Grammatical Range and Accuracy'
            : 'Task Response, Coherence and Cohesion, Lexical Resource, Grammatical Range and Accuracy';

        $prompt = <<<PROMPT
Ты — опытный экзаменатор IELTS Writing. Оцени ответ ученика по заданию ниже.

Тип задания: {$taskType}
Формулировка задания:
{$taskPrompt}

Ответ ученика:
{$answerText}

Оцени по критериям: {$criteria}.
Верни СТРОГО JSON такой структуры (без markdown, без пояснений вне JSON):
{
  "band": число от 0 до 9 с шагом 0.5 (итоговый band score),
  "feedback": "развёрнутый разбор на русском: по каждому из 4 критериев кратко что хорошо и что улучшить, 3-5 предложений на критерий суммарно, плюс 2-3 конкретных примера ошибок из текста ученика с исправлением"
}
PROMPT;

        $raw = trim($this->ask($prompt, json: true));
        $raw = preg_replace(['/^```(json)?/u', '/```$/u'], '', $raw);

        $data = json_decode($raw, true);
        if (! is_array($data) || ! isset($data['band'], $data['feedback'])) {
            throw new \RuntimeException('Не удалось распарсить оценку от Gemini');
        }

        return [
            'band' => (float) $data['band'],
            'feedback' => (string) $data['feedback'],
        ];
    }
}
