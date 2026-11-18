<?php

namespace App\Policies\Chat;

use App\Models\Chat\ChatMessage;
use App\Models\User;

class ChatMessagePolicy
{
    /** Своё сообщение удаляет автор, чужое — только админ. */
    public function delete(User $user, ChatMessage $message): bool
    {
        return $user->id === $message->user_id || $user->isAdmin();
    }
}
