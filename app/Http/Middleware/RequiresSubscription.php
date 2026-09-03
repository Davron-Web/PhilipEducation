<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Закрывает раздел от пользователей без подписки.
 *
 * Применяется как middleware('subscribed:ielts') — имя раздела сверяется
 * со списком payment.paid_sections, поэтому включить или отключить платность
 * раздела можно из конфига, не трогая маршруты.
 */
class RequiresSubscription
{
    public function handle(Request $request, Closure $next, string $section = ''): Response
    {
        $user = $request->user();

        if ($user && $user->canAccessPaidSection($section)) {
            return $next($request);
        }

        // Гостя отправляем логиниться — возможно, подписка у него уже есть.
        if (! $user) {
            return redirect()->guest(route('login'));
        }

        return redirect()
            ->route('billing.plans')
            ->with('info', __('site.billing.section_locked'));
    }
}
