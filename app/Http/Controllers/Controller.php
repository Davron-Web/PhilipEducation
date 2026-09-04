<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Трейт подключён здесь, а не в каждом контроллере: политики — общий для
    // приложения способ проверки прав, и $this->authorize() должен быть
    // доступен везде, иначе проверки расползаются по контроллерам вручную.
    use AuthorizesRequests;
}
