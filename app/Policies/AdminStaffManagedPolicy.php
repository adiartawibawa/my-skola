<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

/**
 * Base Policy untuk model yang Tata Usaha kelola penuh (CRUD), dan
 * Guru sama sekali tidak boleh akses. Dipakai oleh model yang bukan
 * "milik" guru tertentu (Subject, Teacher, Student, User,
 * Announcement, AcademicCalendar) — beda dari ClassRoom/Schedule/dst
 * yang guru boleh lihat versi miliknya sendiri (lihat
 * AdminStaffManagedPolicy yang di-override viewAny/view-nya).
 *
 * Super Admin/Admin Sekolah (akses penuh) dan Kepala Sekolah
 * (read-only) SUDAH ditangani Gate::before() — base class ini tidak
 * perlu tahu-menahu soal keduanya sama sekali.
 */
abstract class AdminStaffManagedPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function view(User $user, $record): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function create(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function update(User $user, $record): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function delete(User $user, $record): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function restore(User $user, $record): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function forceDelete(User $user, $record): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }
}
