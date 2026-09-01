<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canEditBlog();
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->canEditBlog();
    }

    public function create(User $user): bool
    {
        return $user->canWriteBlog();
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->canEditBlog();
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->canEditBlog();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canEditBlog();
    }
}
