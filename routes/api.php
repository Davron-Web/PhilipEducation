<?php

use Illuminate\Support\Facades\Route;

// API маршруты здесь
// Публичные API (без auth)
Route::prefix('public')->group(function () {
    // Здесь маршруты из web.php для public
});

// API с авторизацией
Route::middleware('auth:sanctum')->group(function () {
    // Защищённые API маршруты
});
