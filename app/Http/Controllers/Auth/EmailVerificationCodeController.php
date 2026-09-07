<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationCodeService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmailVerificationCodeController extends Controller
{
    public function store(Request $request, EmailVerificationCodeService $codes): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('user.dashboard', absolute: false));
        }

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Введите код из письма.',
            'code.digits' => 'Код состоит из шести цифр.',
        ]);

        $result = $codes->verify($user, $data['code']);

        if ($result['ok']) {
            $user->markEmailAsVerified();
            event(new Verified($user));

            return redirect()
                ->intended(route('user.dashboard', absolute: false))
                ->with('status', 'email-verified');
        }

        throw ValidationException::withMessages([
            'code' => $this->messageFor($result),
        ]);
    }

    /**
     * Сообщение должно говорить, что делать дальше: истёкший код и
     * исчерпанные попытки требуют нового письма, а простая опечатка — нет.
     */
    private function messageFor(array $result): string
    {
        return match ($result['reason']) {
            'expired' => 'Срок действия кода истёк — запросите новый.',
            'exhausted' => 'Слишком много неверных попыток, код аннулирован. Запросите новый.',
            'missing' => 'Код не найден или уже использован. Запросите новый.',
            default => 'Неверный код. Осталось попыток: '.$result['attemptsLeft'].'.',
        };
    }
}
