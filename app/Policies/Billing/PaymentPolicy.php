<?php

namespace App\Policies\Billing;

use App\Models\Billing\Payment;
use App\Models\User;

/**
 * Платёж видит только его плательщик. Ссылка (reference) случайная, но
 * полагаться на неугадываемость ссылки как на средство доступа нельзя:
 * она попадает в историю браузера, в логи прокси и в реферер.
 */
class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $payment->user_id === $user->id || $user->isAdmin();
    }

    public function pay(User $user, Payment $payment): bool
    {
        return $payment->user_id === $user->id;
    }
}
