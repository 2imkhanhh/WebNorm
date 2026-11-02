<?php

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
