<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Student;
use App\Models\User;

/**
 * Beda dari kebanyakan Policy lain — Student TIDAK extends
 * AdminStaffManagedPolicy lurus, karena Guru di sini bukan "sama
 * sekali tidak boleh akses" (seperti Teacher/User/Announcement),
 * tapi "boleh akses KALAU kaprodi program terkait siswa itu".
 * Data siswa cukup sensitif untuk tidak sekadar diwariskan dari base
 * class yang generik.
 */
class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF
            || ($user->role === RoleEnum::TEACHER && $user->teacher?->currentHeadOfProgramKeahlian() !== null);
    }

    /**
     * Kaprodi boleh lihat siswa kalau kelas AKTIF siswa itu (tahun
     * ajaran aktif) berada di Program Keahlian yang dia pimpin.
     * Siswa yang sudah lulus/pindah kelas di luar program itu tidak
     * ikut, karena currentClassRoom() cuma mengambil enrollment tahun
     * aktif.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->role === RoleEnum::ADMIN_STAFF) {
            return true;
        }

        if ($user->role === RoleEnum::TEACHER) {
            $programKeahlianId = $student->currentClassRoom()?->program_keahlian_id;

            return $user->teacher?->isHeadOfProgramKeahlian($programKeahlianId);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function update(User $user, Student $student): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function restore(User $user, Student $student): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $user->role === RoleEnum::ADMIN_STAFF;
    }
}
