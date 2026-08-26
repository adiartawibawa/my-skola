<?php

namespace App\Models;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $content
 * @property PostStatus $status
 * @property Carbon|null $published_at
 */
#[Fillable([
    'user_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
    'featured_image', 'status', 'published_at', 'scheduled_at',
    'read_time', 'review_note', 'meta_title', 'meta_description',
    'canonical_url', 'og_image',
])]
class Post extends Model
{
    use HasFactory;
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'views_count' => 'integer',
            'likes_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $post->slug ??= static::uniqueSlug($post->title);
            $post->read_time ??= $post->calculateReadTime();
        });

        static::updating(function (Post $post) {
            if ($post->isDirty('content') && ! $post->isDirty('read_time')) {
                $post->read_time = $post->calculateReadTime();
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-".$i++;
        }

        return $slug;
    }

    public function calculateReadTime(int $wordsPerMinute = 200): int
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));

        return max(1, (int) ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Relations
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('status', CommentStatus::APPROVED);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * Toggle like untuk user login atau guest (via session_id).
     * Return true jika sekarang "liked", false jika "unliked".
     */
    public function toggleLike(?int $userId, ?string $sessionId): bool
    {
        $query = $this->likes();

        $existing = $userId
            ? $query->where('user_id', $userId)->first()
            : $query->where('session_id', $sessionId)->first();

        if ($existing) {
            $existing->delete();
            $this->decrement('likes_count');

            return false;
        }

        $this->likes()->create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
        ]);
        $this->increment('likes_count');

        return true;
    }

    public function isLikedBy(?int $userId, ?string $sessionId): bool
    {
        return $userId
            ? $this->likes()->where('user_id', $userId)->exists()
            : $this->likes()->where('session_id', $sessionId)->exists();
    }

    /**
     * Scopes
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', PostStatus::PENDING_REVIEW);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', PostStatus::SCHEDULED)
            ->where('scheduled_at', '>', now());
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereFullText(['title', 'excerpt', 'content'], $term);
    }

    /**
     * Review workflow
     */
    public function submitForReview(): bool
    {
        return $this->update([
            'status' => PostStatus::PENDING_REVIEW,
            'review_note' => null,
        ]);
    }

    public function approve(): bool
    {
        return $this->update([
            'status' => PostStatus::PUBLISHED,
            'published_at' => $this->published_at ?? now(),
            'review_note' => null,
        ]);
    }

    public function reject(?string $note = null): bool
    {
        return $this->update([
            'status' => PostStatus::DRAFT,
            'review_note' => $note,
        ]);
    }
}
