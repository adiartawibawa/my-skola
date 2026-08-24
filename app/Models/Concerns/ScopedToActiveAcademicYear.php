<?php

namespace App\Models\Concerns;

use App\Models\Scopes\ActiveAcademicYearScope;

/**
 * Untuk model dengan kolom academic_year_id LANGSUNG (ClassRoom,
 * ClassRoomStudent, AcademicCalendar). Lihat ActiveAcademicYearScope
 * untuk penjelasan lengkap kenapa ini global scope, bukan filter UI.
 */
trait ScopedToActiveAcademicYear
{
    public static function bootScopedToActiveAcademicYear(): void
    {
        static::addGlobalScope(new ActiveAcademicYearScope);
    }
}
