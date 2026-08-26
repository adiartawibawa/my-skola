<?php

use App\Http\Controllers\BlogFeedController;
use App\Livewire\Blog\PostIndex;
use App\Livewire\Blog\PostShow;
use Illuminate\Support\Facades\Route;

Route::get('/blog/feed.xml', BlogFeedController::class)->name('blog.feed');
Route::get('/blog', PostIndex::class)->name('blog.index');
Route::get('/blog/{post:slug}', PostShow::class)->name('blog.show');
