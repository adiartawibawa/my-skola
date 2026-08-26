<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class BlogFeedController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('blog.feed.xml', now()->addMinutes(30), function () {
            $posts = Post::query()
                ->published()
                ->with(['author', 'category'])
                ->orderByDesc('published_at')
                ->limit(20)
                ->get();

            return view('feed', ['posts' => $posts])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
