<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\User;

/**
 * Base Policy untuk data STRUKTURAL yang perubahannya sengaja dibatasi
 * ke Super Admin/Admin Sekolah saja (Gate::before()) — mengaktifkan
 * Tahun Akademik atau membuat Program Keahlian baru adalah keputusan
 * tingkat kebijakan, bukan operasional harian. Tata Usaha boleh
 * lihat, tidak boleh ubah. Guru sama sekali tidak boleh akses.
 *
 * Asumsi ini bisa disesuaikan kalau ternyata Tata Usaha memang perlu
 * ikut mengelola salah satu dari ini — tinggal pindahkan Policy
 * modelnya untuk extends AdminStaffManagedPolicy saja.
 */
abstract class AdminStaffViewOnlyPolicy
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
        return false;
    }

    public function update(User $user, $record): bool
    {
        return false;
    }

    public function delete(User $user, $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, $record): bool
    {
        return false;
    }

    public function forceDelete(User $user, $record): bool
    {
        return false;
    }
}
