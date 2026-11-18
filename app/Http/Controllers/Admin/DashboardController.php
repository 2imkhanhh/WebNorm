<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Service;
use App\Services\DraftService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'projects_count' => Project::where('is_active', true)->count(),
            'services_count' => Service::where('is_active', true)->count(),
            'blogs_count' => Blog::where('status', 'published')->count(),
            'leads_count' => Contact::where('status', 'new')->count(),
        ];

        $recentContacts = Contact::latest()->take(5)->get();
        $traffic = $this->buildTrafficData();

        return Inertia::render('Dashboard', [
            'metrics' => $metrics,
            'recentContacts' => $recentContacts,
            'traffic' => $traffic,
            'hasAnyDraft' => DraftService::hasAnyDraft(),
        ]);
    }

    /**
     * Build aggregated traffic chart data (7d, 30d, 6m, breakdowns, and top pages).
     *
     * @return array<string, mixed>
     */
    private function buildTrafficData(): array
    {
        $now = Carbon::now();
        $todayStr = $now->toDateString();
        $start6m = $now->copy()->subMonths(5)->startOfMonth();
        $start30d = $now->copy()->subDays(29)->startOfDay();

        $byDate = [];
        $topPages = [];

        if (Schema::hasTable('daily_site_visits')) {
            $rows = DB::table('daily_site_visits')
                ->where('visit_date', '>=', $start6m->toDateString())
                ->orderBy('visit_date')
                ->get();

            foreach ($rows as $row) {
                $byDate[(string) $row->visit_date] = $row;
            }
        }

        $series7d = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $key = $d->toDateString();
            $row = $byDate[$key] ?? null;
            $series7d[] = [
                'label' => $d->format('d/m'),
                'full_date' => $d->format('d/m/Y'),
                'views' => (int) ($row->views ?? 0),
                'visitors' => (int) ($row->unique_visitors ?? 0),
            ];
        }

        $series30d = [];
        $sum30Views = 0;
        $sum30Visitors = 0;
        $sum30Vi = 0;
        $sum30En = 0;
        $sum30Desktop = 0;
        $sum30Mobile = 0;

        for ($i = 29; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i);
            $key = $d->toDateString();
            $row = $byDate[$key] ?? null;
            $views = (int) ($row->views ?? 0);
            $visitors = (int) ($row->unique_visitors ?? 0);

            $sum30Views += $views;
            $sum30Visitors += $visitors;
            $sum30Vi += (int) ($row->vi_views ?? 0);
            $sum30En += (int) ($row->en_views ?? 0);
            $sum30Desktop += (int) ($row->desktop_views ?? 0);
            $sum30Mobile += (int) ($row->mobile_views ?? 0);

            $series30d[] = [
                'label' => $d->format('d/m'),
                'full_date' => $d->format('d/m/Y'),
                'views' => $views,
                'visitors' => $visitors,
            ];
        }

        $monthBuckets = [];
        foreach ($byDate as $dateStr => $row) {
            $ym = substr($dateStr, 0, 7);
            if (! isset($monthBuckets[$ym])) {
                $monthBuckets[$ym] = ['views' => 0, 'visitors' => 0];
            }
            $monthBuckets[$ym]['views'] += (int) $row->views;
            $monthBuckets[$ym]['visitors'] += (int) $row->unique_visitors;
        }

        $series6m = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->startOfMonth()->subMonths($i);
            $ym = $m->format('Y-m');
            $series6m[] = [
                'label' => 'T' . $m->format('n/y'),
                'full_date' => 'Tháng ' . $m->format('m/Y'),
                'views' => (int) ($monthBuckets[$ym]['views'] ?? 0),
                'visitors' => (int) ($monthBuckets[$ym]['visitors'] ?? 0),
            ];
        }

        if (Schema::hasTable('daily_page_views')) {
            $rawTop = DB::table('daily_page_views')
                ->selectRaw('page_path, page_group, SUM(views) as total_views')
                ->where('visit_date', '>=', $start30d->toDateString())
                ->groupBy('page_path', 'page_group')
                ->orderByDesc('total_views')
                ->limit(6)
                ->get();

            $projectSlugs = [];
            $blogSlugs = [];
            foreach ($rawTop as $item) {
                if ($item->page_group === 'work_detail' && str_starts_with($item->page_path, '/work/')) {
                    $projectSlugs[] = substr($item->page_path, strlen('/work/'));
                } elseif ($item->page_group === 'news_detail' && str_starts_with($item->page_path, '/news/')) {
                    $blogSlugs[] = substr($item->page_path, strlen('/news/'));
                }
            }

            $projectsBySlug = ! empty($projectSlugs)
                ? Project::whereIn('slug', $projectSlugs)->get()->keyBy('slug')
                : collect();
            $blogsBySlug = ! empty($blogSlugs)
                ? Blog::whereIn('slug', $blogSlugs)->get()->keyBy('slug')
                : collect();

            foreach ($rawTop as $item) {
                $topPages[] = [
                    'path' => $item->page_path,
                    'label' => $this->resolvePageLabel($item->page_path, $item->page_group, $projectsBySlug, $blogsBySlug),
                    'group' => $item->page_group,
                    'views' => (int) $item->total_views,
                ];
            }
        }

        $todayRow = $byDate[$todayStr] ?? null;

        return [
            'today' => [
                'views' => (int) ($todayRow->views ?? 0),
                'visitors' => (int) ($todayRow->unique_visitors ?? 0),
            ],
            'summary_30d' => [
                'views' => $sum30Views,
                'visitors' => $sum30Visitors,
                'vi_views' => $sum30Vi,
                'en_views' => $sum30En,
                'desktop_views' => $sum30Desktop,
                'mobile_views' => $sum30Mobile,
            ],
            'series' => [
                '7d' => $series7d,
                '30d' => $series30d,
                '6m' => $series6m,
            ],
            'top_pages' => $topPages,
        ];
    }

    private function resolvePageLabel(string $path, string $group, $projectsBySlug, $blogsBySlug): string
    {
        return match ($path) {
            '/' => 'Trang chủ',
            '/about-us' => 'Về chúng tôi',
            '/work' => 'Trang Dự án',
            '/news' => 'Trang Tin tức',
            default => (function () use ($path, $group, $projectsBySlug, $blogsBySlug) {
                if ($group === 'work_detail' && str_starts_with($path, '/work/')) {
                    $slug = substr($path, strlen('/work/'));
                    $proj = $projectsBySlug->get($slug);
                    return $proj ? ('Dự án: ' . ($proj->getTitle('vi') ?: $slug)) : ('Dự án: ' . $slug);
                }
                if ($group === 'news_detail' && str_starts_with($path, '/news/')) {
                    $slug = substr($path, strlen('/news/'));
                    $blog = $blogsBySlug->get($slug);
                    return $blog ? ('Bài viết: ' . ($blog->getTitle('vi') ?: $slug)) : ('Bài viết: ' . $slug);
                }
                return $path;
            })(),
        };
    }
}
