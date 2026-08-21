<?php

namespace App\Models;

use App\Enums\SemesterEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * Aturan Tahun Akademik Indonesia (fixed, tidak bergantung input admin):
 *
 * - Tahun Akademik selalu berlangsung 1 Juli – 30 Juni tahun berikutnya.
 * - Semester Ganjil : 1 Juli – 31 Desember.
 * - Semester Genap  : 1 Januari – 30 Juni.
 *
 * end_date TIDAK diisi manual — selalu diturunkan otomatis dari start_date.
 * mid_semester_ganjil_date / mid_semester_genap_date adalah data referensi
 * "pertengahan semester" (mis. untuk UTS), bukan penentu batas semester.
 */
#[Fillable(
    'code',
    'name',
    'start_date',
    'mid_semester_ganjil_date',
    'mid_semester_genap_date',
    'is_active',
    'description',
)]
class AcademicYear extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'mid_semester_ganjil_date' => 'date',
            'mid_semester_genap_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (AcademicYear $academicYear): void {
            static::validateStartsOnJuly1($academicYear);
            static::deriveEndDate($academicYear);
            static::validateMidSemesterDates($academicYear);
            static::validateNoOverlap($academicYear);
            static::enforceSingleActive($academicYear);
        });
    }

    /**
     * Tahun Akademik di Indonesia selalu dimulai 1 Juli.
     */
    protected static function validateStartsOnJuly1(AcademicYear $academicYear): void
    {
        if (! $academicYear->start_date) {
            throw ValidationException::withMessages([
                'start_date' => 'Tanggal mulai Tahun Akademik wajib diisi.',
            ]);
        }

        if ($academicYear->start_date->month !== 7 || $academicYear->start_date->day !== 1) {
            throw ValidationException::withMessages([
                'start_date' => 'Tahun Akademik harus dimulai pada tanggal 1 Juli.',
            ]);
        }
    }

    /**
     * end_date bukan input bebas — selalu 30 Juni tahun berikutnya,
     * diturunkan dari start_date. Ini mencegah admin salah input
     * periode yang tidak sesuai aturan akademik.
     */
    protected static function deriveEndDate(AcademicYear $academicYear): void
    {
        $academicYear->end_date = $academicYear->start_date->copy()->addYear()->subDay();
    }

    /**
     * Pertengahan semester (jika diisi) harus berada di dalam rentang
     * semester masing-masing — mid Ganjil di semester Ganjil, mid
     * Genap di semester Genap.
     */
    protected static function validateMidSemesterDates(AcademicYear $academicYear): void
    {
        [$ganjilStart, $ganjilEnd] = $academicYear->semesterDateRange(SemesterEnum::GANJIL);
        [$genapStart, $genapEnd] = $academicYear->semesterDateRange(SemesterEnum::GENAP);

        if (
            $academicYear->mid_semester_ganjil_date
            && ! $academicYear->mid_semester_ganjil_date->between($ganjilStart, $ganjilEnd)
        ) {
            throw ValidationException::withMessages([
                'mid_semester_ganjil_date' => "Pertengahan Semester Ganjil harus berada dalam rentang {$ganjilStart->format('d M Y')} – {$ganjilEnd->format('d M Y')}.",
            ]);
        }

        if (
            $academicYear->mid_semester_genap_date
            && ! $academicYear->mid_semester_genap_date->between($genapStart, $genapEnd)
        ) {
            throw ValidationException::withMessages([
                'mid_semester_genap_date' => "Pertengahan Semester Genap harus berada dalam rentang {$genapStart->format('d M Y')} – {$genapEnd->format('d M Y')}.",
            ]);
        }
    }

    /**
     * Cegah dua Tahun Akademik memiliki periode yang tumpang tindih.
     */
    protected static function validateNoOverlap(AcademicYear $academicYear): void
    {
        $query = static::query()
            ->where('start_date', '<=', $academicYear->end_date)
            ->where('end_date', '>=', $academicYear->start_date);

        // Record baru bisa belum punya key saat `saving` terjadi
        // (HasUuids mengisi key di event `creating`, setelah `saving`).
        if ($academicYear->exists) {
            $query->whereKeyNot($academicYear->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'start_date' => 'Periode Tahun Akademik ini tumpang tindih dengan Tahun Akademik lain yang sudah terdaftar.',
            ]);
        }
    }

    /**
     * Pastikan hanya satu Tahun Akademik yang aktif.
     */
    protected static function enforceSingleActive(AcademicYear $academicYear): void
    {
        if (! $academicYear->is_active) {
            return;
        }

        $query = static::query();

        if ($academicYear->exists) {
            $query->whereKeyNot($academicYear->getKey());
        }

        $query->update(['is_active' => false]);
    }

    /**
     * Relation
     */
    public function academicCalendars(): HasMany
    {
        return $this->hasMany(AcademicCalendar::class);
    }

    // SCOPE

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today());
    }

    public function isCurrent(): bool
    {
        return today()->between($this->start_date, $this->end_date);
    }

    /**
     * Resolusi Tahun Akademik default untuk keperluan filter/entry baru:
     * prioritaskan yang eksplisit ditandai aktif, fallback ke yang
     * sedang berjalan berdasarkan tanggal jika tidak ada yang aktif.
     */
    public static function resolveDefault(): ?self
    {
        return static::query()->active()->first()
            ?? static::query()->current()->first();
    }

    /**
     * Rentang tanggal [start, end] untuk semester tertentu di dalam
     * Tahun Akademik ini. Berguna untuk filter kalender per semester
     * dan untuk validasi mid_semester_*.
     */
    public function semesterDateRange(SemesterEnum $semester): array
    {
        $academicStartYear = $this->start_date->year;

        return match ($semester) {
            SemesterEnum::GANJIL => [
                $this->start_date->copy(),
                Carbon::create($academicStartYear, 12, 31),
            ],
            SemesterEnum::GENAP => [
                Carbon::create($academicStartYear + 1, 1, 1),
                $this->end_date->copy(),
            ],
        };
    }

    /**
     * Tanggal pertengahan semester untuk semester tertentu.
     */
    public function midSemesterDate(SemesterEnum $semester): ?Carbon
    {
        return match ($semester) {
            SemesterEnum::GANJIL => $this->mid_semester_ganjil_date,
            SemesterEnum::GENAP => $this->mid_semester_genap_date,
        };
    }

    /**
     * Mendapatkan semester berdasarkan tanggal.
     *
     * Batas semester bersifat FIXED sesuai aturan akademik Indonesia
     * (bukan hasil pembagian 6 bulan yang rawan off-by-one, dan bukan
     * input manual): 1 Jul–31 Des = Ganjil, 1 Jan–30 Jun = Genap.
     */
    public function getSemester(Carbon|string|null $date = null): ?SemesterEnum
    {
        $date = $date ? Carbon::parse($date) : today();

        if ($date->lt($this->start_date) || $date->gt($this->end_date)) {
            return null;
        }

        [, $ganjilEnd] = $this->semesterDateRange(SemesterEnum::GANJIL);

        return $date->lte($ganjilEnd) ? SemesterEnum::GANJIL : SemesterEnum::GENAP;
    }
}
