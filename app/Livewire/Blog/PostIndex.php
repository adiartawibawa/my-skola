<?php

namespace App\Livewire\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PostIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?string $category = null;

    #[Url(history: true)]
    public ?string $tag = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingTag(): void
    {
        $this->resetPage();
    }

    public function setCategory(?string $slug): void
    {
        $this->category = $this->category === $slug ? null : $slug;
        $this->resetPage();
    }

    public function setTag(?string $slug): void
    {
        $this->tag = $this->tag === $slug ? null : $slug;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'category', 'tag');
        $this->resetPage();
    }

    public function render(): View
    {
        $posts = Post::query()
            ->published()
            ->with(['author', 'category', 'tags'])
            ->when($this->search !== '', fn ($query) => $query->search($this->search))
            ->when($this->category, fn ($query) => $query->whereHas(
                'category',
                fn ($query) => $query->where('slug', $this->category)
            ))
            ->when($this->tag, fn ($query) => $query->whereHas(
                'tags',
                fn ($query) => $query->where('slug', $this->tag)
            ))
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.blog.post-index', [
            'posts' => $posts,
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'tags' => Tag::withCount('posts')->orderByDesc('posts_count')->limit(15)->get(),
        ])->layout('components.layouts.blog');
    }
}
