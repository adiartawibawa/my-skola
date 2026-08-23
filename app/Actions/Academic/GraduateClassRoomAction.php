<?php

namespace App\Actions\Academic;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\ClassRoom;
use App\Models\ClassRoomStudent;
use Illuminate\Support\Facades\DB;

class GraduateClassRoomAction
{
    /**
     * Tandai semua siswa Aktif di kelas tingkat akhir ini sebagai
     * Lulus. Ini MENGUBAH baris ClassRoomStudent yang ada (bukan
     * membuat baris baru) karena kelulusan adalah peristiwa yang
     * menutup periode keanggotaan siswa di kelas tersebut.
     */
    public function execute(ClassRoom $classRoom): int
    {
        return DB::transaction(function () use ($classRoom) {
            $graduated = 0;

            $classRoom->activeStudents()->get()->each(function (ClassRoomStudent $enrollment) use ($classRoom, &$graduated) {
                $enrollment->update([
                    'status' => ClassRoomStudentStatusEnum::LULUS->value,
                    'left_at' => $classRoom->academicYear->end_date,
                ]);

                $graduated++;
            });

            return $graduated;
        });
    }
}
