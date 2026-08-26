<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'post_id', 'user_id', 'parent_id', 'guest_name', 'guest_email',
    'content', 'status', 'ip_address', 'user_agent',
])]
class Comment extends Model
{
    use HasFactory;
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => CommentStatus::class,
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function authorName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Anonim';
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::APPROVED);
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
