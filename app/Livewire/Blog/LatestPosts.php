<?php

namespace App\Livewire\Blog;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LatestPosts extends Component
{
    public int $limit = 3;

    public function render(): View
    {
        return view('livewire.blog.latest-posts', [
            'posts' => Post::query()
                ->published()
                ->with(['author', 'category'])
                ->latest('published_at')
                ->limit($this->limit)
                ->get(),
        ]);
    }
}
