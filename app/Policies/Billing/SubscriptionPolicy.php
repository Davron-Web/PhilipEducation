<?php

namespace App\Policies\Billing;

use App\Models\Billing\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    public function view(User $user, Subscription $subscription): bool
    {
        return $subscription->user_id === $user->id || $user->isAdmin();
    }

    public function cancel(User $user, Subscription $subscription): bool
    {
        return $subscription->user_id === $user->id;
    }
}
