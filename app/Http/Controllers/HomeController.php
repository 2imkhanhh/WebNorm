<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected function getPageContentMap(string $pageSlug, Request $request): array
    {
        $contents = PageContent::where('page_slug', $pageSlug)->get();
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
        $projects = Project::where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        $reviews = Review::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.index', compact('pageContents', 'projects', 'services', 'reviews'));
    }

    public function about(Request $request)
    {
        $pageContents = $this->getPageContentMap('/about-us', $request);
        return view('pages.about-us', compact('pageContents'));
    }

    public function work(Request $request)
    {
        $pageContents = $this->getPageContentMap('/work', $request);
        $projects = Project::where('is_active', true)->orderBy('sort_order')->get();
        return view('pages.work', compact('pageContents', 'projects'));
    }

    public function workDetail(Request $request, $slug = null)
    {
        $project = null;
        if ($slug) {
            $project = Project::where('slug', $slug)->first();
        }
        if (!$project) {
            $project = Project::where('is_active', true)->first();
        }

        $otherProjects = Project::where('id', '!=', $project ? $project->id : 0)
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
}
