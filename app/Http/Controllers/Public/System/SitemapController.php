<?php

namespace App\Http\Controllers\Public\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Карта сайта.
 *
 * Перечисляет только те адреса, которые действительно открываются без
 * входа: /, /plans, /login, /register. Весь учебный материал закрыт
 * middleware auth, и добавлять его в sitemap нельзя — поисковик получил бы
 * редирект на логин и счёл карту неверной.
 *
 * Если часть уроков или тем по грамматике сделают доступной для чтения
 * гостям, их достаточно добавить в publicUrls() — формат не изменится.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = view('sitemap', ['urls' => $this->publicUrls()])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /** @return array<int, array{loc: string, priority: string, changefreq: string}> */
    private function publicUrls(): array
    {
        return [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('billing.plans'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('register'), 'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => route('login'), 'priority' => '0.4', 'changefreq' => 'yearly'],
        ];
    }
}
