<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Клиент к собственному ML-сервису определения уровня английского (ml/) —
 * локальный Python-процесс на этой же машине, НЕ внешний AI API (см.
 * ml/README.md: модель обучена самостоятельно на CEFR-SP, TF-IDF +
 * логистическая регрессия). Laravel только вызывает готовый /predict по
 * HTTP, как и GeminiService — но эндпоинт локальный и не облачный.
 */
class CefrPredictionService
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('services.cefr_predictor.url');
    }

    /**
     * Сообщение при обрыве связи — фиксированный текст, чтобы контроллер
     * мог отличить «сервис не запущен» от прочих сбоев без отдельного
     * класса исключения (как и GeminiService различает свои ошибки по
     * тексту сообщения, см. SpeakingBotController::reply()).
     */
    public const UNAVAILABLE_MESSAGE = 'Сервис определения уровня сейчас не запущен. Запустите его: python ml/service/serve.py (см. ml/README.md)';

    /**
     * Диапазон (range_low/range_high) — null у обоих, если модель
     * уверена в одном уровне (см. ml/src/range_rule.py). per_sentence —
     * на что разбил текст sentence_split.py и что предсказано для
     * каждого куска отдельно (для свёрнутого блока «по предложениям» на
     * странице результата) — сама модель обучена на отдельных
     * предложениях, поэтому весь текст больше не скармливается ей одним
     * куском (было — и заметно портило точность, см. ml/README.md).
     *
     * @return array{
     *     level: string,
     *     range_low: ?string,
     *     range_high: ?string,
     *     probabilities: array<string, float>,
     *     sentence_count: int,
     *     per_sentence: array<int, array{text: string, level: string}>
     * }
     *
     * @throws \RuntimeException  сервис не запущен, не отвечает, либо ответил с ошибкой
     */
    public function predict(string $text): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->url}/predict", ['text' => $text]);
        } catch (ConnectionException $e) {
            // Самая частая причина: ml/service/serve.py просто не запущен —
            // это локальный процесс, а не всегда доступный облачный API.
            throw new \RuntimeException(self::UNAVAILABLE_MESSAGE, previous: $e);
        }

        if (! $response->successful()) {
            throw new \RuntimeException('ML-сервис ответил ошибкой '.$response->status().': '.mb_substr($response->body(), 0, 300));
        }

        $level = $response->json('level');
        $probabilities = $response->json('probabilities');

        if (! is_string($level) || ! is_array($probabilities)) {
            throw new \RuntimeException('ML-сервис вернул неожиданный формат ответа');
        }

        $rangeLow = $response->json('range_low');
        $rangeHigh = $response->json('range_high');

        return [
            'level' => $level,
            'range_low' => is_string($rangeLow) ? $rangeLow : null,
            'range_high' => is_string($rangeHigh) ? $rangeHigh : null,
            'probabilities' => $probabilities,
            'sentence_count' => (int) ($response->json('sentence_count') ?? 1),
            'per_sentence' => (array) ($response->json('per_sentence') ?? []),
        ];
    }
}
