<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Dipakai halaman publik. $user nullable karena guest juga boleh baca post published.
     */
    public function view(?User $user, Post $post): bool
    {
        if ($post->status === PostStatus::PUBLISHED) {
            return true;
        }

        return $user && ($user->id === $post->user_id || $user->canEditBlog());
    }

    public function create(User $user): bool
    {
        return $user->canWriteBlog();
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->canEditBlog()) {
            return true;
        }

        return $user->id === $post->user_id && $post->status->isEditableByAuthor();
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->canEditBlog() || $user->id === $post->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->canEditBlog();
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->canEditBlog();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canEditBlog();
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->canEditBlog();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->canEditBlog();
    }

    public function submitForReview(User $user, Post $post): bool
    {
        return $user->id === $post->user_id && $post->status === PostStatus::DRAFT;
    }

    public function approve(User $user, Post $post): bool
    {
        return $user->canEditBlog() && $post->status === PostStatus::PENDING_REVIEW;
    }

    public function reject(User $user, Post $post): bool
    {
        return $user->canEditBlog() && $post->status === PostStatus::PENDING_REVIEW;
    }
}
