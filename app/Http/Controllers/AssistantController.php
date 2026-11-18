<?php

namespace App\Http\Controllers;

use App\Models\Vocabulary\Expression;
use App\Models\Vocabulary\Word;
use App\Services\PhilAssistantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AssistantController extends Controller
{
    public function __construct(protected PhilAssistantService $assistant) {}

    public function chat(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:2000',
            'context_type' => 'nullable|in:word,expression',
            'context_id' => 'nullable|integer',
            // Последние реплики диалога — фронт шлёт их из sessionStorage
            // (см. phil-widget.blade.php), нужны для правила "короткая
            // реплика без языка -> язык предыдущего сообщения".
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'required|in:user,bot',
            'history.*.text' => 'required|string|max:2000',
        ]);

        try {
            $answer = $this->assistant->reply(
                history: $request->input('history', []),
                question: $request->question,
                context: $this->buildContext($request),
                // App::getLocale() уже учитывает session('locale') — её
                // выставляет SetLocale (bootstrap/app.php, глобальный
                // middleware), поэтому отдельно лезть в сессию не нужно.
                fallbackLocale: App::getLocale(),
            );
        } catch (\Throwable $e) {
            return response()->json(['answer' => 'Ошибка: '.$e->getMessage()], 500);
        }

        return response()->json(['answer' => $answer]);
    }

    /**
     * Слово/выражение, которое пользователь смотрит на текущей странице
     * (см. x-phil-widget и @push('phil-context') на word/expression show)
     * — превращаем в короткое описание для системного промпта Gemini.
     */
    private function buildContext(Request $request): ?string
    {
        if ($request->context_type === 'word' && $request->context_id) {
            $word = Word::with('translations')->find($request->context_id);
            if (! $word) {
                return null;
            }

            $parts = ["слово \"{$word->word}\""];
            if ($word->transcription) {
                $parts[] = "транскрипция /{$word->transcription}/";
            }
            if ($word->translations->isNotEmpty()) {
                $parts[] = 'перевод: '.$word->translations->pluck('translation')->join(', ');
            }
            if ($word->example) {
                $parts[] = "пример: \"{$word->example}\"";
            }

            return implode(', ', $parts);
        }

        if ($request->context_type === 'expression' && $request->context_id) {
            $expression = Expression::with('translations')->find($request->context_id);
            if (! $expression) {
                return null;
            }

            $parts = ["выражение \"{$expression->text}\""];
            if ($expression->translations->isNotEmpty()) {
                $parts[] = 'перевод: '.$expression->translations->pluck('translation')->join(', ');
            }
            if ($expression->meaning) {
                $parts[] = "значение: \"{$expression->meaning}\"";
            }
            if ($expression->example) {
                $parts[] = "пример: \"{$expression->example}\"";
            }

            return implode(', ', $parts);
        }

        return null;
    }
}
