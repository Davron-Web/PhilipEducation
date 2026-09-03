<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Payment\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Выдача премиума вручную: ученик получает полный доступ без оплаты.
 */
class UserSubscriptionController extends Controller
{
    public function grant(Request $request, User $user, SubscriptionService $subscriptions): RedirectResponse
    {
        $data = $request->validate([
            // 'lifetime' или число дней.
            'duration' => 'required|string',
            'note' => 'nullable|string|max:255',
        ]);

        $lifetime = $data['duration'] === 'lifetime';
        $days = $lifetime ? null : (int) $data['duration'];

        if (! $lifetime && ($days < 1 || $days > 3650)) {
            return back()->with('error', 'Срок должен быть от 1 до 3650 дней.');
        }

        $subscription = $subscriptions->grant($user, $days, Auth::user(), $data['note'] ?? null);

        // Выдача платного доступа без денег — событие, которое должно
        // оставлять след: иначе потом не понять, откуда у ученика премиум.
        Log::info('Премиум выдан администратором', [
            'user_id' => $user->id,
            'granted_by' => Auth::id(),
            'days' => $days,
            'subscription_id' => $subscription->id,
        ]);

        return back()->with('success', $lifetime
            ? "Пользователю {$user->name} выдан бессрочный премиум."
            : "Пользователю {$user->name} выдан премиум на {$days} дн.");
    }

    public function revoke(User $user, SubscriptionService $subscriptions): RedirectResponse
    {
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return back()->with('error', 'У пользователя нет активной подписки.');
        }

        // Оплаченную подписку админ не отзывает: за неё получены деньги,
        // и снятие доступа было бы спором о возврате, а не правкой данных.
        if (! $subscription->isGift()) {
            return back()->with('error', 'Это оплаченная подписка — отозвать её нельзя, только вернуть деньги через банк.');
        }

        $subscriptions->revoke($subscription);

        Log::info('Подарочный премиум отозван', [
            'user_id' => $user->id,
            'revoked_by' => Auth::id(),
            'subscription_id' => $subscription->id,
        ]);

        return back()->with('success', "Премиум пользователя {$user->name} отозван.");
    }
}
