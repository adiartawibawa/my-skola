<?php

namespace App\Actions\Academic;

use App\Models\AcademicYear;
use App\Models\ClassRoom;

class ResolveNextClassRoomAction
{
    /**
     * Cari (atau buat) kelas tujuan di tahun ajaran berikutnya: program
     * keahlian dan label rombel sama, tingkat naik satu. Mengembalikan
     * null jika kelas sumber sudah tingkat akhir (tidak ada tingkat
     * berikutnya) — kelas seperti ini harus diproses lewat
     * GraduateClassRoomAction, bukan dipromosikan.
     */
    public function execute(ClassRoom $source, AcademicYear $targetAcademicYear): ?ClassRoom
    {
        if ($source->isTerminalGrade()) {
            return null;
        }

        $nextGradeLevel = $source->grade_level + 1;

        if (! in_array($nextGradeLevel, ClassRoom::GRADE_LEVELS, true)) {
            return null;
        }

        return ClassRoom::query()->firstOrCreate(
            [
                'academic_year_id' => $targetAcademicYear->id,
                'program_keahlian_id' => $source->program_keahlian_id,
                'grade_level' => $nextGradeLevel,
                'rombel_label' => $source->rombel_label,
            ],
            [
                'capacity' => $source->capacity,
                'is_active' => true,
            ]
        );
    }
}
