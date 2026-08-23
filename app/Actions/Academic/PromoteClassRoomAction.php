<?php

namespace App\Actions\Academic;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\ClassRoom;
use App\Models\ClassRoomStudent;
use Illuminate\Support\Facades\DB;

class PromoteClassRoomAction
{
    /**
     * Pindahkan semua siswa Aktif dari $source ke $target dengan
     * membuat baris ClassRoomStudent baru di tahun ajaran tujuan.
     * Baris siswa di $source TIDAK diubah — riwayat tahun sebelumnya
     * tetap utuh sebagai histori.
     *
     * Idempoten: siswa yang sudah punya keanggotaan di tahun ajaran
     * tujuan (mis. proses ini pernah dijalankan sebelumnya) dilewati,
     * bukan diduplikasi — constraint unique (student_id, academic_year_id)
     * juga menegakkan ini di level database sebagai jaring pengaman.
     */
    public function execute(ClassRoom $source, ClassRoom $target): int
    {
        return DB::transaction(function () use ($source, $target) {
            $promoted = 0;

            $source->activeStudents()->get()->each(function (ClassRoomStudent $enrollment) use ($target, &$promoted) {
                $alreadyPromoted = ClassRoomStudent::query()
                    ->where('student_id', $enrollment->student_id)
                    ->where('academic_year_id', $target->academic_year_id)
                    ->exists();

                if ($alreadyPromoted) {
                    return;
                }

                ClassRoomStudent::query()->create([
                    'class_room_id' => $target->id,
                    'student_id' => $enrollment->student_id,
                    'joined_at' => $target->academicYear->start_date,
                    'status' => ClassRoomStudentStatusEnum::AKTIF->value,
                ]);

                $promoted++;
            });

            return $promoted;
        });
    }
}
