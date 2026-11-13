<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected function getPageContentMap(string $pageSlug, Request $request, ?array $fieldKeys = null): array
    {
        $query = PageContent::where('page_slug', $pageSlug);
        if ($fieldKeys !== null) {
            $query->whereIn('field_key', $fieldKeys);
        }
        $contents = $query->get();
        $isPreview = auth()->check() && $request->query('preview') === 'draft';
        view()->share('isPreviewDraft', $isPreview);

        $map = [];
        foreach ($contents as $c) {
            if ($isPreview && $c->draft_value !== null) {
                $map[$c->field_key] = $c->draft_value;
            } else {
                $map[$c->field_key] = $c->value;
            }
        }
        return $map;
    }

    public function index(Request $request)
    {
        $pageContents = $this->getPageContentMap('/', $request);
        $projects = Project::where('is_active', true)
            ->where('is_featured', true)
            ->with('categories')
            ->orderBy('sort_order')
            ->get();
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        $reviews = Review::where('is_active', true)->orderBy('sort_order')->get();
        $blogs = Blog::where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->latest('id')
            ->take(10)
            ->get();

        return view('pages.index', compact('pageContents', 'projects', 'services', 'reviews', 'blogs'));
    }

    public function about(Request $request)
    {
        $pageContents = array_merge(
            $this->getPageContentMap('/', $request, ['services_label', 'services_statement', 'services_desc']),
            $this->getPageContentMap('/about-us', $request)
        );
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.about-us', compact('pageContents', 'services'));
    }

    public function work(Request $request)
    {
        $pageContents = $this->getPageContentMap('/work', $request);
        $projects = Project::where('is_active', true)->with('categories')->orderBy('sort_order')->get();
        $categories = ProjectCategory::orderBy('sort_order')->orderBy('id')->get();
        return view('pages.work', compact('pageContents', 'projects', 'categories'));
    }

    public function workDetail(Request $request, $slug = null)
    {
        $project = null;
        if ($slug) {
            $project = Project::with('categories')->where('slug', $slug)->first();
        }
        if (!$project) {
            $project = Project::with('categories')->where('is_active', true)->first();
        }

        $otherProjects = Project::with('categories')->where('id', '!=', $project ? $project->id : 0)
            ->where('is_active', true)
            ->take(3)
            ->get();

        return view('pages.work-detail', compact('project', 'otherProjects'));
    }

    public function news(Request $request)
    {
        $pageContents = $this->getPageContentMap('/news', $request);
        $blogs = Blog::where('status', 'published')->latest('published_at')->get();
        return view('pages.news', compact('pageContents', 'blogs'));
    }

    public function newsDetail(Request $request, $slug = null)
    {
        $blog = null;
        if ($slug) {
            $blog = Blog::where('slug', $slug)->first();
        }
        if (!$blog) {
            $blog = Blog::where('status', 'published')->first();
        }

        $relatedBlogs = Blog::where('id', '!=', $blog ? $blog->id : 0)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return view('pages.news-detail', compact('blog', 'relatedBlogs'));
    }

    public function sitemap()
    {
        $entries = [
            ['vi' => route('home'), 'en' => route('en.home'), 'lastmod' => null],
            ['vi' => route('about'), 'en' => route('en.about'), 'lastmod' => null],
            ['vi' => route('work'), 'en' => route('en.work'), 'lastmod' => null],
            ['vi' => route('news'), 'en' => route('en.news'), 'lastmod' => null],
        ];

        $projects = Project::where('is_active', true)->orderBy('sort_order')->get(['slug', 'updated_at']);
        foreach ($projects as $project) {
            if (!$project->slug) {
                continue;
            }
            $entries[] = [
                'vi' => route('work.detail', $project->slug),
                'en' => route('en.work.detail', $project->slug),
                'lastmod' => $project->updated_at?->toAtomString(),
            ];
        }

        $blogs = Blog::where('status', 'published')->latest('published_at')->get(['slug', 'updated_at']);
        foreach ($blogs as $blog) {
            if (!$blog->slug) {
                continue;
            }
            $entries[] = [
                'vi' => route('news.detail', $blog->slug),
                'en' => route('en.news.detail', $blog->slug),
                'lastmod' => $blog->updated_at?->toAtomString(),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($entries as $item) {
            $viUrl = htmlspecialchars($item['vi'], ENT_XML1, 'UTF-8');
            $enUrl = htmlspecialchars($item['en'], ENT_XML1, 'UTF-8');
            $lastmodTag = $item['lastmod'] ? "\n        <lastmod>" . htmlspecialchars($item['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>" : '';

            foreach ([$viUrl, $enUrl] as $locUrl) {
                $xml .= "    <url>\n";
                $xml .= "        <loc>{$locUrl}</loc>{$lastmodTag}\n";
                $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"vi\" href=\"{$viUrl}\" />\n";
                $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"{$enUrl}\" />\n";
                $xml .= "        <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$viUrl}\" />\n";
                $xml .= "    </url>\n";
            }
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
