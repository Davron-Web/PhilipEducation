<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationNotificationController extends Controller
{
    /** Не чаще раза в минуту на пользователя. */
    private const RESEND_INTERVAL_SECONDS = 60;

    public function store(Request $request, EmailVerificationCodeService $codes): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('user.dashboard', absolute: false));
        }

        $key = 'verification-resend:'.$user->id;

        // Ограничение считаем здесь, а не middleware throttle: тот отдавал
        // страницу «429 Слишком много запросов», из которой не понять, что
        // произошло и сколько ждать. Здесь — возврат на ту же страницу с
        // человеческим сроком.
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->with('resend-wait', $seconds);
        }

        RateLimiter::hit($key, self::RESEND_INTERVAL_SECONDS);

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
