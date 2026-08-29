<?php

use App\Http\Controllers\SitemapController;
use App\Livewire\AppLinksPage;
use App\Livewire\ContactPage;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/tentang-kami', 'pages.about')->name('about');
Route::view('/program', 'pages.program')->name('program.index');
Route::get('/aplikasi', AppLinksPage::class)->name('links.index');
Route::get('/kontak', ContactPage::class)->name('contact');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

require __DIR__.'/blog.php';
