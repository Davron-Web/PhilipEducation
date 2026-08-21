<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RememberPreviousUrl
{
    public function handle(Request $request, Closure $next)
    {
        if (
            !auth()->check() &&
            $request->isMethod('GET')
        ) {
            session([
                'url.intended' => $request->fullUrl()
            ]);
        }

        return $next($request);
    }
}
