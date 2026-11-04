<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

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
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Protected Admin Studio Routes (Requires Auth)
    Route::middleware('auth')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.studio');
        });
        Route::get('/studio', function () {
            return view('admin.studio');
        })->name('admin.studio');
    });
});
