<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Пять попыток в минуту с адреса — на регистрацию, вход и работу с
    // паролем. Вход дополнительно ограничен в LoginRequest по паре
    // email+IP: это ловит подбор пароля к одному аккаунту, а throttle
    // здесь — перебор разных адресов с одного IP.
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    /*
     * Подтверждение почты отключено — маршруты сняты.
     *
     * Механизм не удалён: контроллеры, сервис кодов, письмо и таблица
     * email_verification_codes остались на месте. Чтобы включить обратно,
     * нужно вернуть сюда четыре маршрута, добавить модели User контракт
     * MustVerifyEmail и middleware 'verified' на разделы уроков, словаря
     * и тестов. Ждём переезда на хостинг, где почта настроена с SPF и
     * DKIM: сейчас письма с локальной машины уходят в спам Gmail.
     */

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->middleware('throttle:5,1')
        ->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
