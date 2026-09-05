<?php

namespace App\Models;

use App\Enums\ClassRoomStudentStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'student_id', 'program_keahlian_id', 'tahun_lulus',
    'nis_klaim', 'is_verified', 'verified_by', 'verified_at',
])]
class AlumniProfile extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function programKeahlian(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * true untuk lulusan yang sudah melalui jalur digital (observer
     * otomatis, terhubung ke Student asli) — dipakai UI untuk membedakan
     * badge "Data Sistem" vs "Menunggu Verifikasi".
     */
    public function isFromDigitalTrack(): bool
    {
        return $this->student_id !== null;
    }

    /**
     * Jalur digital: SELALU baca live dari ClassRoomStudent lewat
     * student_id — jangan pernah percaya kolom tahun_lulus di baris
     * ini untuk kasus ini (sengaja dibiarkan null, lihat observer).
     * Jalur legacy: tidak ada apa pun untuk dirujuk, jadi kolom
     * tahun_lulus di sini MEMANG satu-satunya sumber data.
     */
    public function resolvedTahunLulus(): ?int
    {
        if (! $this->student_id) {
            return $this->tahun_lulus;
        }

        $enrollment = $this->student?->classRoomEnrollments()
            ->withoutGlobalScopes()
            ->where('status', ClassRoomStudentStatusEnum::LULUS->value)
            ->latest('left_at')
            ->first();

        return ($enrollment?->left_at ?? $enrollment?->joined_at)?->year;
    }

    public function resolvedProgramKeahlianName(): ?string
    {
        if (! $this->student_id) {
            return $this->programKeahlian?->name;
        }

        $enrollment = $this->student?->classRoomEnrollments()
            ->withoutGlobalScopes()
            ->where('status', ClassRoomStudentStatusEnum::LULUS->value)
            ->latest('left_at')
            ->first();

        // withoutGlobalScopes(): sama alasannya seperti AlumniTable::resolveClassRoom() —
        // ClassRoom kelas terakhir siswa bisa dari tahun ajaran yang sudah tidak aktif.
        $classRoom = $enrollment
            ? ClassRoom::query()->withoutGlobalScopes()->find($enrollment->class_room_id)
            : null;

        return $classRoom?->programKeahlian?->name;
    }
}
