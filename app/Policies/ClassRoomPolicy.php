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
     * Guru boleh membuka SATU kelas kalau memang dia wali kelas AKTIF
     * di kelas itu. currentHomeroomTeacher() sudah pakai
     * withoutGlobalScopes() sendiri (lihat ClassRoom model) jadi aman
     * dipanggil di sini terlepas Tahun Akademik mana yang aktif.
     */
    public function view(User $user, $record): bool
    {
        if ($user->role === RoleEnum::ADMIN_STAFF) {
            return true;
        }

        if ($user->role === RoleEnum::TEACHER) {
            return $record->currentHomeroomTeacher()?->user_id === $user->id;
        }

        return false;
    }
}
