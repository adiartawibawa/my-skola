<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends AdminStaffManagedPolicy
{
    /**
     * Menugaskan capability (blog.write/blog.editor dkk) adalah privilege
     * escalation — sengaja TIDAK ikut update() yang diwariskan dari
     * AdminStaffManagedPolicy (Tata Usaha). Selalu false di sini; hanya
     * Super Admin/Admin Sekolah yang lolos, itu pun lewat Gate::before()
     * di AppServiceProvider (bukan lewat method ini).
     */
    public function assignCapabilities(User $user, ?User $record = null): bool
    {
        return false;
    }
}
