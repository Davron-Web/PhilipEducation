<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Заголовки безопасности для всех ответов.
 *
 * Content-Security-Policy сознательно не добавляем: интерфейс тянет Tailwind
 * и Alpine с CDN и держит стили с обработчиками прямо в разметке, поэтому
 * любая осмысленная политика сейчас сломала бы вёрстку. Её вводят вместе с
 * переносом ассетов в сборку, а не отдельным заголовком.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Запрет встраивать сайт в чужой фрейм: без него страницу можно
        // накрыть прозрачным слоем и собирать нажатия ученика (clickjacking).
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Браузер не должен угадывать тип файла: загруженная «картинка» с
        // HTML внутри иначе может выполниться как страница.
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // На чужие сайты уходит только домен, без пути: адреса вроде
        // /verify-email/12/<хеш> не должны попадать в чужие логи через Referer.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Микрофон нужен разговорному боту, поэтому оставлен себе;
        // камера и геолокация не используются нигде.
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), geolocation=(), payment=(), microphone=(self)'
        );

        return $response;
    }
}
