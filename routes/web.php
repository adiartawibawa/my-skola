<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

require __DIR__.'/blog.php';
