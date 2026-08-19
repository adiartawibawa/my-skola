<?php

namespace App\Models;

use App\Enums\Enums\SemesterEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('code', 'name', 'start_date', 'end_date', 'is_active', 'description')]
class AcademicYear extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Boot method untuk menambahkan event
     */
    protected static function booted(): void
    {
        static::saving(function (AcademicYear $academicYear): void {
            if ($academicYear->is_active) {
                static::query()
                    ->whereKeyNot($academicYear->getKey())
                    ->update([
                        'is_active' => false,
                    ]);
            }
        });
    }

    /**
     * Relation
     */
    public function academicCalendars(): HasMany
    {
        return $this->hasMany(AcademicCalendar::class);
    }

    // SCOPE

    /**
     * Scope untuk mendapatkan tahun ajaran aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk tahun ajaran yang sedang berlangsung
     */
    public function scopeCurrent($query)
    {
        return $query
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today());
    }

    /**
     * Cek apakah tahun ajaran sedang berlangsung
     */
    public function isCurrent(): bool
    {
        return today()->between(
            $this->start_date,
            $this->end_date,
        );
    }

    /**
     * Mendapatkan semester berdasarkan tanggal
     */
    public function getSemester(Carbon|string|null $date = null): ?SemesterEnum
    {
        $date = $date ? Carbon::parse($date) : today();

        if ($date->lt($this->start_date) || $date->gt($this->end_date)) {
            return null;
        }

        $midDate = $this->start_date->copy()->addMonths(6);

        return $date->lte($midDate) ? SemesterEnum::GANJIL : SemesterEnum::GENAP;
    }
}
