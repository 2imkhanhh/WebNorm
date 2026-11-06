<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Review;
use App\Models\Service;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'pages_count' => PageContent::distinct('page_slug')->count('page_slug') ?: 4,
            'projects_count' => Project::where('is_active', true)->count(),
            'services_count' => Service::where('is_active', true)->count(),
            'reviews_count' => Review::where('is_active', true)->count(),
            'blogs_count' => Blog::where('status', 'published')->count(),
            'leads_count' => Contact::where('status', 'new')->count(),
        ];

        $recentContacts = Contact::latest()->take(5)->get();

        return Inertia::render('Dashboard', [
            'metrics' => $metrics,
            'recentContacts' => $recentContacts,
        ]);
    }
}
