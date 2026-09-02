<?php

namespace App\Services;

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

        $res = Http::withHeaders(['x-goog-api-key' => $this->key])
            ->timeout(90)
            ->post("{$this->url}/{$this->model}:generateContent", $payload);

        if ($res->failed()) {
            throw new \RuntimeException('Gemini HTTP '.$res->status().': '.mb_substr($res->body(), 0, 300));
        }

        return $res->json('candidates.0.content.parts.0.text', '');
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
