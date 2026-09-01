<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canWriteBlog();
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->canEditBlog() || $user->id === $comment->post->user_id;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->canEditBlog();
    }

    /**
     * Pemilik post boleh bersihkan spam di postingannya sendiri;
     * editor boleh hapus komentar di post mana pun.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->canEditBlog() || $user->id === $comment->post->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->canEditBlog();
    }

    /**
     * Approve/reject sengaja TIDAK ikut logika delete() di atas —
     * ini wewenang moderasi terpusat, bukan sekadar "punya post-nya".
     */
    public function approve(User $user, Comment $comment): bool
    {
        return $user->canEditBlog();
    }

    public function reject(User $user, Comment $comment): bool
    {
        return $user->canEditBlog();
    }
}
