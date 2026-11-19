<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminInactivity
{
    public const TIMEOUT_SECONDS = 30 * 60;

    /**
     * Automatically log out admin users after 30 minutes of inactivity.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $now = time();
            $lastActivity = $request->session()->get('admin_last_activity');

            if ($lastActivity !== null && ($now - (int) $lastActivity) >= self::TIMEOUT_SECONDS) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $loginUrl = route('admin.login', ['timeout' => 1]);

                if ($request->header('X-Inertia')) {
                    return Inertia::location($loginUrl);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Phiên đăng nhập đã hết hạn',
                        'redirect' => $loginUrl,
                    ], 401);
                }

                return redirect()->to($loginUrl);
            }

            $request->session()->put('admin_last_activity', $now);
        }

        return $next($request);
    }
}
