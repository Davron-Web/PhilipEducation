<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Языки, между которыми можно переключаться. Ключ — код локали
     * (папка в lang/), значение — как язык называется на самом себе.
     */
    public const SUPPORTED = [
        'ru' => 'Русский',
        'en' => 'English',
        'tg' => 'Тоҷикӣ',
    ];

    /**
     * Ставит язык интерфейса из сессии. Если в сессии ничего нет,
     * остаётся язык по умолчанию из config/app.php.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if ($locale && array_key_exists($locale, self::SUPPORTED)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
