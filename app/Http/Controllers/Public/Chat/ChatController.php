<?php

namespace App\Http\Controllers\Public\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Chat\StoreChatMessageRequest;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatReport;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Общая комната — одна на всю платформу, поэтому без параметров:
     * просто последние 50 видимых сообщений, старые сверху. Дальнейшая
     * история (до самого старого id) подгружается через loadMore().
     */
    public function index(): View
    {
        return view('public.chat.index', $this->bootstrapData());
    }

    /**
     * GET /chat/messages/initial — то же самое, чем открывается index(), но
     * JSON: нужен выезжающей панели чата (x-chat-drawer), которая живёт на
     * любой странице сайта, а не только на /chat, и поэтому не может
     * получить эти данные через обычный рендер Blade-страницы.
     */
    public function initial(): JsonResponse
    {
        return response()->json($this->bootstrapData());
    }

    /** @return array{initialMessages: \Illuminate\Support\Collection, hasMoreHistory: bool, oldestId: ?int, isBanned: bool} */
    private function bootstrapData(): array
    {
        $messages = ChatMessage::with('user')
            ->visible()
            ->latest('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return [
            'initialMessages' => $messages->map(fn (ChatMessage $m) => $this->serialize($m)),
            // Если сообщений меньше 50 — истории до них точно нет, кнопку
            // "Показать ещё" не показываем вообще, а не просто отключаем.
            'hasMoreHistory' => $messages->count() === 50,
            'oldestId' => $messages->first()?->id,
            'isBanned' => Auth::user()->isChatBanned(),
        ];
    }

    /** GET /chat/messages?after_id=N — опрос на новые сообщения (каждые 5 сек с фронта). */
    public function poll(Request $request): JsonResponse
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = ChatMessage::with('user')
            ->visible()
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->limit(100)
            ->get();

        return response()->json([
            'messages' => $messages->map(fn (ChatMessage $m) => $this->serialize($m)),
        ]);
    }

    /** GET /chat/messages/before?before_id=N — более старая история ("Показать ещё" / скролл вверх). */
    public function loadMore(Request $request): JsonResponse
    {
        $beforeId = (int) $request->query('before_id', 0);

        $messages = ChatMessage::with('user')
            ->visible()
            ->where('id', '<', $beforeId)
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'messages' => $messages->map(fn (ChatMessage $m) => $this->serialize($m)),
            'hasMore' => $messages->count() === 50,
        ]);
    }

    public function store(StoreChatMessageRequest $request): JsonResponse
    {
        abort_if($request->user()->isChatBanned(), 403, 'Вы временно не можете писать в общий чат.');

        $message = ChatMessage::create([
            'user_id' => $request->user()->id,
            'body' => (string) $request->string('body')->trim(),
        ]);

        $message->setRelation('user', $request->user());

        return response()->json(['message' => $this->serialize($message)], 201);
    }

    public function report(ChatMessage $message, Request $request): JsonResponse
    {
        // Не $request->validate() — на этом приложении JSON-рендер ошибок
        // решает путь (только /api/*, см. bootstrap/app.php), а не
        // Accept-заголовок, поэтому автоматическое исключение здесь
        // отправило бы fetch() на редирект вместо 422 с телом ошибок.
        $validator = Validator::make($request->all(), [
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            ChatReport::create([
                'chat_message_id' => $message->id,
                'reported_by' => $request->user()->id,
                'reason' => $request->input('reason'),
            ]);
        } catch (QueryException) {
            // unique(chat_message_id, reported_by) — повторная жалоба от
            // того же человека на то же сообщение не ошибка, а no-op:
            // повторный клик по кнопке не должен выглядеть как сбой.
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(ChatMessage $message): JsonResponse
    {
        $this->authorize('delete', $message);

        $message->update([
            'is_deleted' => true,
            'deleted_by' => Auth::id(),
        ]);

        return response()->json(['ok' => true]);
    }

    /** @return array<string, mixed> */
    private function serialize(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
            'created_at_human' => $message->created_at->format('H:i'),
            'is_own' => $message->user_id === Auth::id(),
            'user' => [
                'id' => $message->user_id,
                'name' => $message->user->name,
                // avatar_url теперь никогда не бывает null (см. User::
                // avatarUrl() — свой выбор из галереи или детерминированный
                // запасной из того же набора), доп. подстановка не нужна.
                'avatar_url' => $message->user->avatar_url,
                'initial' => $message->user->initial(),
            ],
        ];
    }
}
