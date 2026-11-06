<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Client Routes (Blade Views with Dynamic Database Content)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [HomeController::class, 'about'])->name('about');
Route::get('/work', [HomeController::class, 'work'])->name('work');
Route::get('/work/{slug?}', [HomeController::class, 'workDetail'])->name('work.detail');
Route::get('/news', [HomeController::class, 'news'])->name('news');
Route::get('/news/{slug?}', [HomeController::class, 'newsDetail'])->name('news.detail');

// Client Contact Drawer submission
Route::post('/contact/submit', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:50',
        'services' => 'nullable|array',
        'budget' => 'nullable|string',
        'message' => 'nullable|string',
    ]);

    \App\Models\Contact::create($validated);

    return response()->json(['success' => true, 'message' => 'Cảm ơn bạn đã liên hệ! Norm sẽ phản hồi sớm nhất.']);
})->name('contact.submit');

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

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Pages Content Editor
        Route::get('/pages', [PageContentController::class, 'index'])->name('admin.pages');
        Route::post('/pages/update', [PageContentController::class, 'update'])->name('admin.pages.update');

        // Projects
        Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects');
        Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

        // Services
        Route::get('/services', [ServiceController::class, 'index'])->name('admin.services');
        Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');

        // News & Blogs
        Route::get('/news', [BlogController::class, 'index'])->name('admin.news');
        Route::post('/news', [BlogController::class, 'store'])->name('admin.news.store');
        Route::put('/news/{blog}', [BlogController::class, 'update'])->name('admin.news.update');
        Route::delete('/news/{blog}', [BlogController::class, 'destroy'])->name('admin.news.destroy');

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews');
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
        Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

        // Redirect old studio route
        Route::get('/studio/{any?}', function () {
            return redirect()->route('admin.dashboard');
        });
    });
});
