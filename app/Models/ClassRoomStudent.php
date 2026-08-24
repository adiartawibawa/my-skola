<?php

namespace App\Models;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\Concerns\ScopedToActiveAcademicYear;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[Fillable(
    'class_room_id',
    'student_id',
    'academic_year_id',
    'joined_at',
    'left_at',
    'status',
)]
class ClassRoomStudent extends Model
{
    use HasFactory;
    use HasUuids;
    use ScopedToActiveAcademicYear;

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'left_at' => 'date',
            'status' => ClassRoomStudentStatusEnum::class,
        ];
    }

    /**
     * Relations
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Boot method untuk sinkronisasi academic_year_id dan validasi periode
     */
    protected static function booted(): void
    {
        static::saving(function (ClassRoomStudent $enrollment): void {
            $classRoom = static::resolveClassRoom($enrollment);

            static::syncAcademicYear($enrollment, $classRoom);
            static::defaultJoinedAt($enrollment, $classRoom);
            static::validateDateOrder($enrollment);
            static::validateWithinAcademicYear($enrollment, $classRoom);
            static::validateExitStatusHasLeftAt($enrollment);
        });
    }

    /**
     * Query internal untuk mengetahui kelas induknya sendiri lewat
     * class_room_id, bukan query "apa yang boleh dilihat user". Kalau
     * ikut ter-scope, enrollment untuk kelas di tahun yang sedang tidak
     * aktif (mis. saat seeding, atau saat proses kenaikan kelas
     * menyentuh tahun berikutnya) akan gagal menemukan classroom-nya
     * sendiri dan academic_year_id tidak pernah ter-set.
     */
    protected static function resolveClassRoom(ClassRoomStudent $enrollment): ClassRoom
    {
        $classRoom = $enrollment->relationLoaded('classRoom') ? $enrollment->getRelation('classRoom') : null;

        if (! $classRoom && $enrollment->class_room_id) {
            $classRoom = ClassRoom::query()
                ->withoutGlobalScopes()
                ->find($enrollment->class_room_id);
        }

        if (! $classRoom) {
            throw ValidationException::withMessages([
                'class_room_id' => 'Keanggotaan siswa harus terikat pada Kelas.',
            ]);
        }

        return $classRoom;
    }

    /**
     * academic_year_id selalu diturunkan dari class_room-nya, bukan
     * diisi manual — inilah yang membuat unique constraint
     * (student_id, academic_year_id) di database bisa diandalkan untuk
     * menegakkan "1 siswa hanya 1 kelas per tahun ajaran".
     */
    protected static function syncAcademicYear(ClassRoomStudent $enrollment, ClassRoom $classRoom): void
    {
        if (! $classRoom->academic_year_id) {
            throw ValidationException::withMessages([
                'class_room_id' => 'Kelas terkait tidak memiliki academic_year_id yang valid.',
            ]);
        }

        $enrollment->academic_year_id = $classRoom->academic_year_id;

    }

    protected static function defaultJoinedAt(ClassRoomStudent $enrollment, ClassRoom $classRoom): void
    {
        if (! $enrollment->joined_at) {
            // Ambil AcademicYear tanpa terhalang ActiveAcademicYearScope
            $academicYear = $classRoom->academicYear()->withoutGlobalScopes()->first();

            if ($academicYear) {
                $enrollment->joined_at = $academicYear->start_date;
            }
        }
    }

    protected static function validateDateOrder(ClassRoomStudent $enrollment): void
    {
        if ($enrollment->left_at && $enrollment->joined_at && $enrollment->left_at->lt($enrollment->joined_at)) {
            throw ValidationException::withMessages([
                'left_at' => 'Tanggal keluar tidak boleh sebelum tanggal bergabung.',
            ]);
        }
    }

    protected static function validateWithinAcademicYear(ClassRoomStudent $enrollment, ClassRoom $classRoom): void
    {
        $academicYear = $classRoom->academicYear;

        if (! $academicYear || ! $enrollment->joined_at) {
            return;
        }

        $end = $enrollment->left_at ?? $enrollment->joined_at;

        if ($enrollment->joined_at->lt($academicYear->start_date) || $end->gt($academicYear->end_date)) {
            throw ValidationException::withMessages([
                'joined_at' => "Periode keanggotaan harus berada dalam Tahun Akademik ({$academicYear->start_date->format('d M Y')} – {$academicYear->end_date->format('d M Y')}).",
            ]);
        }
    }

    /**
     * Status selain Aktif (lulus, keluar, pindah_sekolah, tidak_naik)
     * menandakan siswa sudah tidak lagi aktif di kelas ini — wajib
     * mengisi left_at supaya periode keanggotaannya jelas.
     */
    protected static function validateExitStatusHasLeftAt(ClassRoomStudent $enrollment): void
    {
        if ($enrollment->status?->isExitStatus() && ! $enrollment->left_at) {
            throw ValidationException::withMessages([
                'left_at' => 'Tanggal keluar wajib diisi untuk status selain Aktif.',
            ]);
        }
    }
}
