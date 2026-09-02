<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\ClassRoom;
use App\Models\User;

class ClassRoomPolicy extends AdminStaffManagedPolicy
{
    /**
     * Guru boleh MELIHAT daftar (viewAny) — tapi baris yang benar-benar
     * tampil sudah dibatasi lewat ClassRoomResource::getEloquentQuery()
     * ke kelas yang jadi wali kelasnya sendiri. Policy ini cuma
     * menentukan boleh/tidaknya, bukan yang membatasi barisnya.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN_STAFF, RoleEnum::TEACHER], true);
    }

    /**
     * Guru boleh membuka SATU kelas kalau:
     * (a) dia wali kelas AKTIF di kelas itu, ATAU
     * (b) dia kaprodi AKTIF di program keahlian kelas itu — kaprodi
     *     otomatis bisa lihat SEMUA kelas di program-nya, tidak cuma
     *     kelas yang dia jadi wali kelasnya.
     * currentHomeroomTeacher() sudah pakai withoutGlobalScopes()
     * sendiri (lihat ClassRoom model) jadi aman dipanggil di sini
     * terlepas Tahun Akademik mana yang aktif.
     */
    public function view(User $user, $classRoom): bool
    {
        if ($user->role === RoleEnum::ADMIN_STAFF) {
            return true;
        }

        if ($user->role === RoleEnum::TEACHER) {
            return $classRoom->currentHomeroomTeacher()?->user_id === $user->id
                || $user->teacher?->isHeadOfProgramKeahlian($classRoom->program_keahlian_id);
        }

        return false;
    }
}
