<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\RequiresSubscription;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Прокси перед приложением — cloudflared в туннеле, nginx на боевом
        // сервере — подключается к нему с этой же машины, поэтому доверяем
        // только петле. Доверять '*' нельзя: тогда любой, кто достучится до
        // порта приложения напрямую, подделает X-Forwarded-Host и получит
        // письмо для сброса пароля со ссылкой на свой домен.
        //
        // Без этого Laravel видит запрос из-за https-туннеля как обычный http
        // и собирает ссылки с http:// на странице, отданной по https.
        $middleware->trustProxies(
            at: ['127.0.0.1', '::1'],
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO,
        );

        // Язык интерфейса берётся из сессии на каждом веб-запросе.
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Заголовки безопасности — на все ответы, включая API и ошибки.
        $middleware->append(SecurityHeaders::class);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'subscribed' => RequiresSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->create();
