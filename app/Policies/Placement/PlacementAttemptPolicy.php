<?php

namespace App\Policies\Placement;

use App\Models\Placement\PlacementAttempt;
use App\Models\User;

/** В попытке лежат ответы конкретного ученика — чужую показывать нельзя. */
class PlacementAttemptPolicy
{
    public function view(User $user, PlacementAttempt $attempt): bool
    {
        return $attempt->user_id === $user->id || $user->isAdmin();
    }
}
