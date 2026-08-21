<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // ✅ ИСПРАВЛЕНО: Проверяем role_id == 1 (или на всякий случай строковое значение 'admin')
        if ($user->role_id == 1 || (isset($user->role) && $user->role === 'admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Для обычных пользователей
        return redirect()->intended(route('user.dashboard'));
        // Если у вас нет route('user.dashboard'), замените на route('public.home') или '/'
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
