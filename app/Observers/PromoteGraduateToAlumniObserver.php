<?php

namespace App\Observers;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Enums\RoleEnum;
use App\Models\AlumniProfile;
use App\Models\ClassRoomStudent;

/**
 * Jalur A (Alumni Digital) — lihat diskusi arsitektur. Status LULUS
 * secara khusus (BUKAN keluar/pindah_sekolah/tidak_naik yang sama-sama
 * "exit status" tapi maknanya bukan kelulusan) memicu promosi role
 * otomatis + pembuatan AlumniProfile terverifikasi penuh, karena
 * datanya berasal dari catatan internal sekolah sendiri — bukan klaim
 * self-report seperti jalur legacy.
 */
class PromoteGraduateToAlumniObserver
{
    public function saved(ClassRoomStudent $enrollment): void
    {
        if ($enrollment->status !== ClassRoomStudentStatusEnum::LULUS) {
            return;
        }

        $student = $enrollment->relationLoaded('student')
            ? $enrollment->getRelation('student')
            : $enrollment->student()->first();

        $user = $student?->user;

        if (! $user || $user->role === RoleEnum::ALUMNI) {
            return;
        }

        $classRoom = $enrollment->classRoom()->withoutGlobalScopes()->first();

        $user->update(['role' => RoleEnum::ALUMNI]);

        AlumniProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'student_id' => $student->id,
                'program_keahlian_id' => $classRoom?->program_keahlian_id,
                'tahun_lulus' => ($enrollment->left_at ?? $enrollment->joined_at)?->year ?? now()->year,
                'is_verified' => true,
                'verified_by' => null,
                'verified_at' => now(),
            ]
        );
    }
}
