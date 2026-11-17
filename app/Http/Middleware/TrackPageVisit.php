<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisit
{
    /**
     * Route names eligible for public traffic tracking.
     */
    private const TRACKABLE_ROUTES = [
        'home' => 'home',
        'en.home' => 'home',
        'about' => 'about',
        'en.about' => 'about',
        'work' => 'work',
        'en.work' => 'work',
        'work.detail' => 'work_detail',
        'en.work.detail' => 'work_detail',
        'news' => 'news',
        'en.news' => 'news',
        'news.detail' => 'news_detail',
        'en.news.detail' => 'news_detail',
    ];

    /**
     * Common crawler / automated client substrings to exclude from analytics.
     */
    private const BOT_PATTERNS = [
        'bot',
        'crawl',
        'spider',
        'slurp',
        'bingpreview',
        'mediapartners',
        'headless',
        'lighthouse',
        'pagespeed',
        'gtmetrix',
        'pingdom',
        'uptime',
        'curl/',
        'wget/',
        'python-requests',
        'python-urllib',
        'go-http-client',
        'axios/',
        'node-fetch',
        'postman',
        'insomnia',
        'facebookexternalhit',
        'twitterbot',
        'linkedinbot',
        'telegrambot',
        'whatsapp',
        'discordbot',
        'zalo',
        'ahrefs',
        'semrush',
        'mj12bot',
        'dotbot',
        'bytespider',
        'gptbot',
        'claudebot',
    ];

    /**
     * Handle an incoming request and record public page visit metrics after response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->recordVisit($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $response->getStatusCode() !== 200) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Inertia')) {
            return false;
        }

        if (Auth::check() || $request->query('preview') === 'draft') {
            return false;
        }

        $purpose = strtolower((string) ($request->header('Sec-Purpose') ?: $request->header('Purpose')));
        if (str_contains($purpose, 'prefetch')) {
            return false;
        }

        $routeName = (string) ($request->route()?->getName() ?? '');
        if (! isset(self::TRACKABLE_ROUTES[$routeName])) {
            return false;
        }

        $userAgent = strtolower(trim((string) $request->userAgent()));
        if ($userAgent === '') {
            return false;
        }

        foreach (self::BOT_PATTERNS as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return false;
            }
        }

        return true;
    }

    private function recordVisit(Request $request): void
    {
        try {
            $now = now();
            $today = $now->toDateString();
            $routeName = (string) ($request->route()?->getName() ?? '');
            $pageGroup = self::TRACKABLE_ROUTES[$routeName] ?? 'other';
            $locale = str_starts_with($routeName, 'en.') ? 'en' : 'vi';

            $ua = strtolower((string) $request->userAgent());
            $isMobile = (bool) preg_match('/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/', $ua);
            $deviceType = $isMobile ? 'mobile' : 'desktop';

            // Normalize path by stripping /en prefix so VI and EN views aggregate per content page
            $rawPath = '/' . ltrim($request->path(), '/');
            $normalizedPath = preg_replace('#^/en(?=/|$)#', '', $rawPath) ?: '/';
            $normalizedPath = mb_substr($normalizedPath, 0, 191);
            $pathHash = md5($normalizedPath);

            // Privacy-safe daily visitor fingerprint
            $visitorHash = hash('sha256', implode('|', [
                (string) $request->ip(),
                $ua,
                $today,
                (string) config('app.key'),
            ]));

            $inserted = DB::table('daily_visitor_hashes')->insertOrIgnore([
                'visit_date' => $today,
                'visitor_hash' => $visitorHash,
                'created_at' => $now,
            ]);
            $isNewVisitor = $inserted > 0;

            // Upsert daily site-wide summary (1 row per day)
            DB::table('daily_site_visits')->insertOrIgnore([
                'visit_date' => $today,
                'views' => 0,
                'unique_visitors' => 0,
                'vi_views' => 0,
                'en_views' => 0,
                'desktop_views' => 0,
                'mobile_views' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $siteIncrements = [
                'views' => DB::raw('views + 1'),
                $locale === 'en' ? 'en_views' : 'vi_views' => DB::raw(($locale === 'en' ? 'en_views' : 'vi_views') . ' + 1'),
                $deviceType === 'mobile' ? 'mobile_views' : 'desktop_views' => DB::raw(($deviceType === 'mobile' ? 'mobile_views' : 'desktop_views') . ' + 1'),
                'updated_at' => $now,
            ];
            if ($isNewVisitor) {
                $siteIncrements['unique_visitors'] = DB::raw('unique_visitors + 1');
            }

            DB::table('daily_site_visits')
                ->where('visit_date', $today)
                ->update($siteIncrements);

            // Upsert per-page daily view counter
            DB::table('daily_page_views')->insertOrIgnore([
                'visit_date' => $today,
                'path_hash' => $pathHash,
                'page_path' => $normalizedPath,
                'page_group' => $pageGroup,
                'views' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('daily_page_views')
                ->where('visit_date', $today)
                ->where('path_hash', $pathHash)
                ->update([
                    'views' => DB::raw('views + 1'),
                    'updated_at' => $now,
                ]);

            // Opportunistically prune visitor hashes older than 2 days (~1% of visits)
            if (random_int(1, 100) === 1) {
                DB::table('daily_visitor_hashes')
                    ->where('visit_date', '<', $now->copy()->subDays(2)->toDateString())
                    ->delete();
            }
        } catch (\Throwable) {
            // Never interrupt public page rendering if analytics write fails
        }
    }
}

