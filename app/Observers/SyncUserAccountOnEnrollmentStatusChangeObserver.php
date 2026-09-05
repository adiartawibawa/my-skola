<?php

namespace App\Observers;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Enums\RoleEnum;
use App\Models\AlumniProfile;
use App\Models\ClassRoomStudent;
use App\Models\User;

class SyncUserAccountOnEnrollmentStatusChangeObserver
{
    public function saved(ClassRoomStudent $enrollment): void
    {
        // Guard penting: tanpa ini, method di bawah akan dievaluasi
        // ulang setiap kali baris ini disimpan untuk alasan APA PUN
        // (mis. TU cuma mengoreksi joined_at) — bukan cuma saat status
        // benar-benar berubah. wasChanged() sengaja dicek di sini
        // (bukan di masing-masing method) supaya satu guard berlaku
        // untuk semua cabang status sekaligus.
        if (! $enrollment->wasChanged('status')) {
            return;
        }

        match ($enrollment->status) {
            ClassRoomStudentStatusEnum::LULUS => $this->promoteToAlumni($enrollment),
            ClassRoomStudentStatusEnum::KELUAR,
            ClassRoomStudentStatusEnum::PINDAH_SEKOLAH => $this->suspendAccount($enrollment),
            // AKTIF, TIDAK_NAIK: sengaja tidak diapa-apakan. Tidak Naik
            // berarti siswa masih bersekolah di sini (mengulang
            // tingkat), akun harus tetap aktif sebagai role student.
            default => null,
        };
    }

    protected function resolveUser(ClassRoomStudent $enrollment): ?User
    {
        $student = $enrollment->relationLoaded('student')
            ? $enrollment->getRelation('student')
            : $enrollment->student()->first();

        return $student?->user;
    }

    protected function promoteToAlumni(ClassRoomStudent $enrollment): void
    {
        $user = $this->resolveUser($enrollment);

        if (! $user || $user->role === RoleEnum::ALUMNI) {
            return;
        }

        $student = $enrollment->relationLoaded('student')
            ? $enrollment->getRelation('student')
            : $enrollment->student()->first();

        $user->update(['role' => RoleEnum::ALUMNI]);

        AlumniProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_id' => $student->id,
                'is_verified' => true,
                'verified_by' => null,
                'verified_at' => now(),
            ]
        );
    }

    /**
     * Keluar/Pindah Sekolah: soft-delete akun — SoftDeletes bawaan
     * Laravel otomatis mengecualikan baris ber-deleted_at dari query
     * autentikasi (login form maupun panel Filament), tanpa perlu
     * kolom/pengecekan tambahan. Data tetap ada untuk audit & bisa
     * di-restore TU lewat RestoreAction di EditUser kalau ternyata
     * input ini keliru.
     */
    protected function suspendAccount(ClassRoomStudent $enrollment): void
    {
        $user = $this->resolveUser($enrollment);

        if (! $user || $user->trashed()) {
            return;
        }

        $user->delete();
    }
}
