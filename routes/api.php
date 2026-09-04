<?php

use App\Http\Controllers\Api\V1\AchievementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
|
| Версия в адресе с самого начала: у мобильного приложения обновления
| выходят не тогда, когда обновляется сервер, и старые версии должны
| продолжать работать после смены формата ответов.
|
| Авторизация — токенами Sanctum: приложение не держит cookie-сессию.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Вход. Ограничение по частоте — защита от перебора паролей.
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    });

    // Тарифы видны и без входа: страница цен нужна до регистрации.
    Route::get('plans', [SubscriptionController::class, 'plans'])->name('plans');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::get('lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
        Route::post('lessons/{lesson}/complete', [LessonController::class, 'complete'])->name('lessons.complete');

        Route::get('tests', [TestController::class, 'index'])->name('tests.index');
        Route::get('tests/{test}', [TestController::class, 'show'])->name('tests.show');
        Route::post('tests/{test}/submit', [TestController::class, 'submit'])->name('tests.submit');

        Route::get('progress', ProgressController::class)->name('progress');

        Route::get('achievements', [AchievementController::class, 'index'])->name('achievements.index');
        Route::get('achievements/mine', [AchievementController::class, 'mine'])->name('achievements.mine');

        Route::get('subscription', [SubscriptionController::class, 'current'])->name('subscription');
    });
});
