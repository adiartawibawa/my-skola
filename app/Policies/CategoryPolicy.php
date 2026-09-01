<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canEditBlog();
    }

    public function view(User $user, Category $category): bool
    {
        return $user->canEditBlog();
    }

    /**
     * Tetap terbuka untuk penulis biasa — dipakai saat createOptionForm
     * di Select kategori pada PostForm (bikin kategori baru sambil menulis).
     */
    public function create(User $user): bool
    {
        return $user->canWriteBlog();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->canEditBlog();
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->canEditBlog();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canEditBlog();
    }
}
