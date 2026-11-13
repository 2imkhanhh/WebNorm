<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve and apply active website locale ('vi' default, or 'en').
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queryLang = $request->query('lang');
        if (in_array($queryLang, ['vi', 'en'], true)) {
            $locale = $queryLang;
        } else {
            $locale = session('locale', config('app.locale', 'vi'));
            if (! in_array($locale, ['vi', 'en'], true)) {
                $locale = 'vi';
            }
        }

        app()->setLocale($locale);

        return $next($request);
    }
}

