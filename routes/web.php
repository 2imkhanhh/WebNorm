<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\ProjectCategoryController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function (\Illuminate\Http\Request $request, string $locale) {
    $targetLocale = $locale === 'en' ? 'en' : 'vi';
    $referer = (string) $request->headers->get('referer', '');
    $refererPath = parse_url($referer, PHP_URL_PATH) ?: '/';
    $basePath = preg_replace('#^/en(?=/|$)#', '', '/' . ltrim($refererPath, '/')) ?: '/';
    $targetPath = $targetLocale === 'en'
        ? ($basePath === '/' ? '/en' : '/en' . $basePath)
        : $basePath;

    $refererQuery = [];
    parse_str((string) parse_url($referer, PHP_URL_QUERY), $refererQuery);
    if (auth()->check() && ($refererQuery['preview'] ?? null) === 'draft') {
        $targetPath .= '?preview=draft';
    }

    return redirect($targetPath);
})->whereIn('locale', ['vi', 'en'])->name('lang.switch');

Route::get('/vi', fn() => redirect('/', 301));
Route::get('/vi/{any}', fn(string $any) => redirect('/' . ltrim($any, '/'), 301))->where('any', '.*');

Route::middleware(\App\Http\Middleware\TrackPageVisit::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about-us', [HomeController::class, 'about'])->name('about');
    Route::get('/work', [HomeController::class, 'work'])->name('work');
    Route::get('/work/{slug?}', [HomeController::class, 'workDetail'])->name('work.detail');
    Route::get('/news', [HomeController::class, 'news'])->name('news');
    Route::get('/news/{slug?}', [HomeController::class, 'newsDetail'])->name('news.detail');

    Route::prefix('en')->name('en.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/about-us', [HomeController::class, 'about'])->name('about');
        Route::get('/work', [HomeController::class, 'work'])->name('work');
        Route::get('/work/{slug?}', [HomeController::class, 'workDetail'])->name('work.detail');
        Route::get('/news', [HomeController::class, 'news'])->name('news');
        Route::get('/news/{slug?}', [HomeController::class, 'newsDetail'])->name('news.detail');
    });
});

Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

Route::post('/contact/submit', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => ['nullable', 'string', 'max:20', 'regex:/^(?=(?:.*\d){8,15})[0-9+\-\.\s\(\)]+$/'],
        'services' => 'nullable|array',
        'budget' => 'nullable|string',
        'message' => 'nullable|string',
    ], [
        'name.required' => __('site.contact_err_name'),
        'email.required' => __('site.contact_err_email_required'),
        'email.email' => __('site.contact_err_email_invalid'),
        'phone.regex' => __('site.contact_err_phone_invalid'),
        'phone.max' => __('site.contact_err_phone_max'),
    ]);

    $contact = \App\Models\Contact::create($validated);

    try {
        \App\Services\MailConfigService::sendContactNotification($contact);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Contact email notification failed: ' . $e->getMessage());
    }

    return response()->json(['success' => true, 'message' => __('site.contact_success')]);
})->middleware('throttle:10,1')->name('contact.submit');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:15,1')->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['auth', \App\Http\Middleware\CheckAdminInactivity::class])->group(function () {
        Route::post('/ping', [AuthController::class, 'ping'])->name('admin.ping');

        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Pages Content Editor
        Route::get('/pages', [PageContentController::class, 'index'])->name('admin.pages');
        Route::post('/pages/update', [PageContentController::class, 'update'])->name('admin.pages.update');
        Route::post('/pages/discard-draft', [PageContentController::class, 'discardDraft'])->name('admin.pages.discard-draft');

        // Projects
        Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects');
        Route::post('/projects/draft', [ProjectController::class, 'saveDraft'])->name('admin.projects.draft');
        Route::post('/projects/discard-draft', [ProjectController::class, 'discardDraft'])->name('admin.projects.discard-draft');
        Route::post('/projects/publish', [ProjectController::class, 'publish'])->name('admin.projects.publish');
        Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

        // Project Categories 
        Route::post('/project-categories', [ProjectCategoryController::class, 'store'])->name('admin.project-categories.store');
        Route::put('/project-categories/{category}', [ProjectCategoryController::class, 'update'])->name('admin.project-categories.update');
        Route::delete('/project-categories/{category}', [ProjectCategoryController::class, 'destroy'])->name('admin.project-categories.destroy');

        // Services
        Route::get('/services', [ServiceController::class, 'index'])->name('admin.services');
        Route::post('/services/draft', [ServiceController::class, 'saveDraft'])->name('admin.services.draft');
        Route::post('/services/discard-draft', [ServiceController::class, 'discardDraft'])->name('admin.services.discard-draft');
        Route::post('/services/publish', [ServiceController::class, 'publish'])->name('admin.services.publish');
        Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');

        // News & Blogs
        Route::get('/news', [BlogController::class, 'index'])->name('admin.news');
        Route::post('/news/draft', [BlogController::class, 'saveDraft'])->name('admin.news.draft');
        Route::post('/news/discard-draft', [BlogController::class, 'discardDraft'])->name('admin.news.discard-draft');
        Route::post('/news/publish', [BlogController::class, 'publish'])->name('admin.news.publish');
        Route::post('/news', [BlogController::class, 'store'])->name('admin.news.store');
        Route::put('/news/{blog}', [BlogController::class, 'update'])->name('admin.news.update');
        Route::delete('/news/{blog}', [BlogController::class, 'destroy'])->name('admin.news.destroy');

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews');
        Route::post('/reviews/draft', [ReviewController::class, 'saveDraft'])->name('admin.reviews.draft');
        Route::post('/reviews/discard-draft', [ReviewController::class, 'discardDraft'])->name('admin.reviews.discard-draft');
        Route::post('/reviews/publish', [ReviewController::class, 'publish'])->name('admin.reviews.publish');
        Route::post('/reviews', [ReviewController::class, 'store'])->name('admin.reviews.store');
        Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('admin.reviews.update');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

        // Leads & Contacts
        Route::get('/leads', [ContactController::class, 'index'])->name('admin.leads');
        Route::patch('/leads/{contact}', [ContactController::class, 'updateStatus'])->name('admin.leads.update');
        Route::delete('/leads/{contact}', [ContactController::class, 'destroy'])->name('admin.leads.destroy');

        // Media Library
        Route::get('/media', [MediaController::class, 'index'])->name('admin.media');
        Route::post('/media/upload', [MediaController::class, 'upload'])->name('admin.media.upload');
        Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('admin.media.destroy');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/settings/draft', [SettingController::class, 'saveDraft'])->name('admin.settings.draft');
        Route::post('/settings/discard-draft', [SettingController::class, 'discardDraft'])->name('admin.settings.discard-draft');
        Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::post('/settings/test-mail', [SettingController::class, 'testMail'])->name('admin.settings.test-mail');

        // Redirect old studio route
        Route::get('/studio/{any?}', function () {
            return redirect()->route('admin.dashboard');
        });
    });
});
