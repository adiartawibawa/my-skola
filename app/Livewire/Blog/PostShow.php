<?php

namespace App\Livewire\Blog;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class PostShow extends Component
{
    use AuthorizesRequests;

    public Post $post;

    public string $content = '';

    public ?string $guestName = '';

    public ?string $guestEmail = '';

    public ?int $parentId = null;

    public bool $hasLiked = false;

    public function mount(Post $post): void
    {
        $this->authorize('view', $post);

        $this->post = $post;
        $this->hasLiked = $post->isLikedBy(auth()->id(), session()->getId());

        $this->registerView();
    }

    public function toggleLike(): void
    {
        $this->hasLiked = $this->post->toggleLike(auth()->id(), session()->getId());
        $this->post->refresh();
    }

    /**
     * Hitung view sekali per sesi per post, supaya tidak spam refresh.
     */
    protected function registerView(): void
    {
        $viewed = session()->get('viewed_posts', []);

        if (! in_array($this->post->id, $viewed, true)) {
            $this->post->increment('views_count');
            session()->push('viewed_posts', $this->post->id);
        }
    }

    public function replyTo(int $commentId): void
    {
        $this->parentId = $commentId;
    }

    public function cancelReply(): void
    {
        $this->parentId = null;
    }

    public function submitComment(): void
    {
        $rules = [
            'content' => ['required', 'string', 'max:2000'],
        ];

        if (! auth()->check()) {
            $rules['guestName'] = ['required', 'string', 'max:100'];
            $rules['guestEmail'] = ['required', 'email', 'max:255'];
        }

        $this->validate($rules);

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => $this->parentId,
            'guest_name' => auth()->check() ? null : $this->guestName,
            'guest_email' => auth()->check() ? null : $this->guestEmail,
            'content' => $this->content,
            'status' => CommentStatus::PENDING,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->reset('content', 'guestName', 'guestEmail', 'parentId');

        session()->flash('comment_success', 'Komentar kamu terkirim dan akan tampil setelah disetujui moderator.');
    }

    public function render(): View
    {
        $comments = $this->post->comments()
            ->approved()
            ->topLevel()
            ->with(['user', 'replies' => fn ($query) => $query->approved()->with('user')])
            ->latest()
            ->get();

        $relatedPosts = Post::query()
            ->published()
            ->when($this->post->category_id, fn ($query) => $query->where('category_id', $this->post->category_id))
            ->whereKeyNot($this->post->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('livewire.blog.post-show', [
            'comments' => $comments,
            'relatedPosts' => $relatedPosts,
        ])->layout('layouts.blog', [
            'title' => $this->post->meta_title ?: $this->post->title,
            'description' => $this->post->meta_description ?: $this->post->excerpt,
            'ogImage' => $this->post->og_image ?: $this->post->featured_image,
            'canonicalUrl' => $this->post->canonical_url ?: route('blog.show', $this->post),
        ]);
    }
}
