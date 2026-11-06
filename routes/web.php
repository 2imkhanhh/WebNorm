<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Client Routes (Blade Views)
Route::get('/', function () {
    return view('pages.index');
})->name('home');

Route::get('/about-us', function () {
    return view('pages.about-us');
})->name('about');

Route::get('/work', function () {
    return view('pages.work');
})->name('work');

Route::get('/work/detail', function () {
    return view('pages.work-detail');
})->name('work.detail');

Route::get('/news', function () {
    return view('pages.news');
})->name('news');

Route::get('/news/detail', function () {
    return view('pages.news-detail');
})->name('news.detail');

// Admin Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Dashboard Routes (Inertia.js)
    Route::middleware('auth')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('admin.dashboard');
        Route::get('/pages', fn() => Inertia::render('Pages'))->name('admin.pages');
        Route::get('/projects', fn() => Inertia::render('Projects'))->name('admin.projects');
        Route::get('/services', fn() => Inertia::render('Services'))->name('admin.services');
        Route::get('/news', fn() => Inertia::render('Blogs'))->name('admin.news');
        Route::get('/reviews', fn() => Inertia::render('Reviews'))->name('admin.reviews');
        Route::get('/leads', fn() => Inertia::render('Contacts'))->name('admin.leads');
        Route::get('/media', fn() => Inertia::render('Media'))->name('admin.media');
        Route::get('/settings', fn() => Inertia::render('Settings'))->name('admin.settings');

        // Redirect old studio route to dashboard
        Route::get('/studio/{any?}', function () {
            return redirect()->route('admin.dashboard');
        });
    });
});
