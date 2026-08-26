<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), function () {
            $posts = Post::query()
                ->published()
                ->orderByDesc('published_at')
                ->get(['slug', 'updated_at', 'published_at']);

            $staticUrls = [
                ['loc' => route('home'), 'lastmod' => now(), 'priority' => '1.0', 'changefreq' => 'daily'],
                ['loc' => route('blog.index'), 'lastmod' => $posts->max('updated_at') ?? now(), 'priority' => '0.9', 'changefreq' => 'daily'],
            ];

            $postUrls = $posts->map(fn (Post $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at,
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ]);

            return view('sitemap', ['urls' => collect($staticUrls)->concat($postUrls)])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
