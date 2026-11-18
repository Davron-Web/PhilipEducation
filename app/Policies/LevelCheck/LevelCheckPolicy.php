<?php

namespace App\Policies\LevelCheck;

use App\Models\LevelCheck\LevelCheck;
use App\Models\User;

/** Результат проверки уровня — личные данные ученика, чужой смотреть нельзя. */
class LevelCheckPolicy
{
    public function view(User $user, LevelCheck $levelCheck): bool
    {
        return $levelCheck->user_id === $user->id || $user->isAdmin();
    }
}
