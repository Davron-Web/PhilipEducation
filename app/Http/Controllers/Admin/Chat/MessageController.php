<?php

namespace App\Http\Controllers\Admin\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat\ChatMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = ChatMessage::with('user')
            ->latest()
            ->when(request('search'), function ($query, $search) {
                $query->where('body', 'like', "%{$search}%");
            })
            ->paginate(30)
            ->withQueryString();

        return view('admin.chat.messages.index', compact('messages'));
    }

    public function destroy(ChatMessage $message): RedirectResponse
    {
        $message->update([
            'is_deleted' => true,
            'deleted_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.chat.messages.index')
            ->with('success', 'Сообщение скрыто');
    }

    /**
     * Фиксированный срок вместо выбора даты — сознательное упрощение MVP
     * (см. план): снимается кнопкой "Разбанить", то есть по факту это
     * "забанен, пока админ не решит иначе", а не точный срок в 10 лет.
     */
    public function banUser(User $user): RedirectResponse
    {
        $user->update(['chat_banned_until' => now()->addYears(10)]);

        return back()->with('success', "Пользователь {$user->name} заблокирован в чате");
    }

    public function unbanUser(User $user): RedirectResponse
    {
        $user->update(['chat_banned_until' => null]);

        return back()->with('success', "Пользователь {$user->name} разблокирован в чате");
    }
}
