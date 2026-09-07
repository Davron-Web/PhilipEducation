<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Role;
use App\Rules\NotDisposableEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                // rfc — формат по стандарту, dns — у домена есть MX-запись.
                // Вместе они отсекают опечатки вроде «gmial.com» и выдуманные
                // домены до того, как письмо уйдёт в никуда.
                'email:rfc,dns',
                'max:255',
                'unique:'.User::class,
                new NotDisposableEmail,
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // Стандартные сообщения Laravel здесь бесполезны: и опечатка в
            // домене, и несуществующий домен дают одинаковое «must be a valid
            // email address», по которому непонятно, что чинить.
            'email.email' => 'Такой почтовый домен не существует. Проверьте адрес — возможно, опечатка в части после «@».',
            'email.unique' => 'Этот адрес уже зарегистрирован. Войдите или восстановите пароль.',
            'email.lowercase' => 'Введите адрес строчными буквами.',
            'email.required' => 'Укажите адрес электронной почты.',
            'name.required' => 'Укажите имя.',
            'password.required' => 'Придумайте пароль.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['description' => 'Ученик платформы']);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $studentRole->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('user.dashboard', absolute: false));
    }
}
