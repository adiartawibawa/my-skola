<?php

namespace App\Models;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\Concerns\ScopedToActiveAcademicYear;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

#[Fillable(
    'academic_year_id',
    'program_keahlian_id',
    'grade_level',
    'rombel_label',
    'capacity',
    'is_active',
    'description',
)]
class ClassRoom extends Model
{
    use HasFactory;
    use HasUuids;
    use ScopedToActiveAcademicYear;
    use SoftDeletes;

    /**
     * Tingkat kelas yang valid untuk SMK. 13 disediakan untuk program
     * keahlian 4 tahun (mis. Pelayaran/Perkapalan) — bukan aturan umum.
     */
    public const GRADE_LEVELS = [10, 11, 12, 13];

    private const GRADE_LEVEL_ROMAN = [
        10 => 'X',
        11 => 'XI',
        12 => 'XII',
        13 => 'XIII',
    ];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relations
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function programKeahlian(): BelongsTo
    {
        return $this->belongsTo(ProgramKeahlian::class);
    }

    public function classRoomTeachers(): HasMany
    {
        return $this->hasMany(ClassRoomTeacher::class);
    }

    public function classRoomStudents(): HasMany
    {
        return $this->hasMany(ClassRoomStudent::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessors
     */
    public function getGradeLevelRomanAttribute(): string
    {
        return self::GRADE_LEVEL_ROMAN[$this->grade_level] ?? (string) $this->grade_level;
    }

    /**
     * Nama tampilan yang di-generate, mis. "X TKJ A" — bukan input
     * bebas, supaya konsisten dan otomatis ikut berubah kalau kode
     * program keahlian di-rename.
     */
    public function getFullNameAttribute(): string
    {
        return trim(sprintf(
            '%s %s %s',
            $this->grade_level_roman,
            $this->programKeahlian?->code,
            $this->rombel_label,
        ));
    }

    public function getStudentCountAttribute(): int
    {
        return $this->activeStudents()->count();
    }

    /**
     * Wali kelas yang sedang menjabat (ended_at masih null).
     *
     * withoutGlobalScopes(): method ini dipanggil pada SATU instance
     * ClassRoom yang sudah spesifik (lewat class_room_id) — scope tahun
     * aktif milik ClassRoomTeacher tidak relevan dan justru salah di
     * sini kalau $this bukan kelas dari tahun aktif (mis. saat melihat
     * kelas historis lewat toggle "Tampilkan Semua Tahun Akademik").
     */
    public function currentHomeroomTeacher(): ?Teacher
    {
        return $this->classRoomTeachers()
            ->withoutGlobalScopes()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first()
            ?->teacher;
    }

    /**
     * Siswa dengan status Aktif di kelas ini.
     *
     * withoutGlobalScopes(): sama alasannya seperti
     * currentHomeroomTeacher() di atas — relevan untuk kelas historis.
     */
    public function activeStudents(): HasMany
    {
        return $this->classRoomStudents()
            ->withoutGlobalScopes()
            ->where('status', ClassRoomStudentStatusEnum::AKTIF);
    }

    public function isFull(): bool
    {
        return $this->capacity !== null && $this->student_count >= $this->capacity;
    }

    /**
     * Apakah kelas ini adalah tingkat akhir untuk program keahliannya
     * (mis. XII untuk program 3 tahun, XIII untuk program 4 tahun).
     * Dipakai untuk menentukan apakah kelas diproses "naik kelas" (ke
     * tingkat berikutnya) atau "diluluskan" (tidak ada tingkat berikutnya).
     */
    public function isTerminalGrade(): bool
    {
        $durationYears = $this->programKeahlian?->duration_years ?? 3;

        return $this->grade_level >= (9 + $durationYears);
    }

    /**
     * Boot method untuk validasi
     */
    protected static function booted(): void
    {
        static::saving(function (ClassRoom $classRoom): void {
            static::validateGradeLevel($classRoom);
            static::validateUniqueCombination($classRoom);
        });
    }

    protected static function validateGradeLevel(ClassRoom $classRoom): void
    {
        if (! in_array($classRoom->grade_level, self::GRADE_LEVELS, true)) {
            throw ValidationException::withMessages([
                'grade_level' => 'Tingkat kelas tidak valid.',
            ]);
        }
    }

    /**
     * Cegah kombinasi tahun ajaran + program keahlian + tingkat + label
     * rombel yang sama persis (mis. dua "X TKJ A" di tahun ajaran yang
     * sama). Constraint unique di database sudah menegakkan ini secara
     * mutlak — validasi ini hanya untuk pesan error yang ramah di UI.
     */
    protected static function validateUniqueCombination(ClassRoom $classRoom): void
    {
        // withoutAcademicYearScope(): $classRoom->academic_year_id bisa
        // saja BUKAN tahun aktif (mis. dibuat lewat proses kenaikan
        // kelas untuk tahun berikutnya). Tanpa ini, scope global akan
        // menambahkan where academic_year_id = aktif yang bentrok
        // dengan where eksplisit di bawah, membuat pengecekan duplikat
        // ini selalu lolos padahal belum tentu benar.
        $query = static::query()
            ->withoutGlobalScopes()
            ->where('academic_year_id', $classRoom->academic_year_id)
            ->where('program_keahlian_id', $classRoom->program_keahlian_id)
            ->where('grade_level', $classRoom->grade_level)
            ->where('rombel_label', $classRoom->rombel_label);

        if ($classRoom->exists) {
            $query->whereKeyNot($classRoom->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'rombel_label' => 'Kombinasi tingkat, program keahlian, dan label rombel ini sudah ada di tahun ajaran tersebut.',
            ]);
        }
    }
}
