<?php

namespace App\Policies\Test;

use App\Models\Test\TestAttempt;
use App\Models\User;

/** В попытке лежат ответы ученика — чужую показывать нельзя. */
class TestAttemptPolicy
{
    public function view(User $user, TestAttempt $attempt): bool
    {
        return $attempt->user_id === $user->id || $user->isAdmin();
    }
}
