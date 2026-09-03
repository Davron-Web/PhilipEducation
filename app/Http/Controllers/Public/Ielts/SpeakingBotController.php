<?php

namespace App\Http\Controllers\Public\Ielts;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Разговорная практика с ботом: ученик говорит голосом, бот отвечает
 * текстом (браузер его озвучивает), после разговора — разбор ошибок.
 *
 * История диалога живёт на клиенте и приходит с каждым запросом: так
 * не нужно хранить состояние на сервере, а ученик может закрыть вкладку
 * без последствий. Длину истории ограничиваем — и ради стоимости
 * запросов к Gemini, и чтобы запрос не разрастался.
 */
class SpeakingBotController extends Controller
{
    /** Сколько последних реплик отправляем в модель. */
    private const HISTORY_LIMIT = 20;

    /**
     * Валидируем вручную через Validator, а не $request->validate(): приложение
     * рендерит JSON-ошибки только для api/*, поэтому обычный validate() отдал бы
     * fetch'у редирект вместо 422.
     */
    private const HISTORY_RULES = [
        'history' => 'present|array|max:100',
        'history.*.role' => 'required|string|in:user,bot',
        'history.*.text' => 'required|string|max:1000',
    ];

    public function room(): View
    {
        return view('public.ielts.speaking.bot');
    }

    public function reply(Request $request, GeminiService $gemini): JsonResponse
    {
        $validator = Validator::make($request->all(), self::HISTORY_RULES + [
            'level' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Неверные данные диалога', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $history = array_slice($data['history'], -self::HISTORY_LIMIT);

        if (empty($history)) {
            return response()->json([
                'reply' => "Hi! I'm Robo, your speaking partner. What would you like to talk about today?",
            ]);
        }

        $level = $data['level'] ?? 'B1';

        $system = <<<SYS
You are Robo, a friendly robot who helps someone practise spoken English.

Rules:
- Reply in English only, in 1-3 short sentences. This is speech, not an essay:
  the reply will be read aloud, so keep it short and natural.
- Match roughly CEFR level {$level}. Use simple words for A1-A2.
- Always end with a question, so the conversation keeps going.
- Never correct the student's mistakes during the conversation — they get a
  full review at the end, and interrupting to correct kills the flow.
- The student's words come from speech recognition, so expect typos and
  missing punctuation. Interpret them charitably; if a phrase is truly
  unclear, ask them to say it again.
- Do not use emoji, markdown or stage directions — everything you write
  is spoken out loud.
SYS;

        try {
            $reply = $gemini->converse($history, $system);
        } catch (\Throwable $e) {
            // Ученику показываем безобидную реплику, но причину пишем в лог —
            // иначе сбои Gemini выглядят как «бот молчит» без следов.
            Log::warning('Speaking bot reply failed: '.$e->getMessage());

            // Исчерпанная квота — не «не расслышал»: пусть ученик знает, что
            // дело не в его произношении и надо просто подождать.
            $reply = str_contains($e->getMessage(), 'HTTP 429')
                ? 'Too many requests right now. Please wait a moment and try again.'
                : "Sorry, I didn't catch that. Could you say it again?";

            return response()->json(['reply' => $reply, 'error' => true], 200);
        }

        return response()->json(['reply' => $reply ?: "Could you say that again?"]);
    }

    /** Разбор разговора после завершения. */
    public function report(Request $request, GeminiService $gemini): JsonResponse
    {
        $validator = Validator::make($request->all(), self::HISTORY_RULES + [
            'unclear' => 'present|array|max:50',
            'unclear.*' => 'string|max:60',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Неверные данные диалога', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $userTurns = collect($data['history'])->where('role', 'user');

        if ($userTurns->isEmpty()) {
            return response()->json([
                'summary' => 'Вы ещё ничего не сказали — разбирать нечего.',
                'mistakes' => [],
                'pronunciation' => [],
            ]);
        }

        try {
            $report = $gemini->reviewConversation($data['history'], $data['unclear']);
        } catch (\Throwable $e) {
            Log::warning('Speaking bot report failed: '.$e->getMessage());

            return response()->json([
                'summary' => 'Не удалось получить разбор — попробуйте позже.',
                'mistakes' => [],
                'pronunciation' => [],
            ], 200);
        }

        return response()->json($report);
    }
}
