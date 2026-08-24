<?php

namespace App\Models\Concerns;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;

/**
 * Untuk model TANPA kolom academic_year_id langsung, tapi terikat ke
 * Tahun Akademik lewat relasi classRoom() (mis. ClassRoomTeacher).
 * Perilakunya sama seperti ScopedToActiveAcademicYear, hanya cara
 * membatasinya lewat whereHas ke tabel class_rooms.
 */
trait ScopedToActiveAcademicYearViaClassRoom
{
    public static function bootScopedToActiveAcademicYearViaClassRoom(): void
    {
        static::addGlobalScope('activeAcademicYear', function (Builder $builder) {
            $activeAcademicYearId = AcademicYear::resolveDefault()?->id;

            if ($activeAcademicYearId) {
                $builder->whereHas(
                    'classRoom',
                    fn (Builder $query) => $query->where('academic_year_id', $activeAcademicYearId),
                );
            }
        });
    }
}
