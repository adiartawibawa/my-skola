<?php

namespace Database\Seeders;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ClassRoomTeacher;
use App\Models\ProgramKeahlian;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassRoomRombelSeeder extends Seeder
{
    /**
     * Data contoh: 3 program keahlian, masing-masing 2 rombel paralel
     * (A/B) di tiap tingkat X–XII pada Tahun Akademik yang aktif —
     * lengkap dengan wali kelas dan 20 siswa per rombel.
     *
     * Aman dijalankan berulang: firstOrCreate dipakai di setiap level
     * supaya tidak membuat duplikat.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::resolveDefault()
            ?? AcademicYear::factory()->create(['is_active' => true]);

        $programs = [
            ['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan', 'duration_years' => 3],
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'duration_years' => 3],
            ['code' => 'OTO', 'name' => 'Teknik Otomotif', 'duration_years' => 3],
        ];

        foreach ($programs as $program) {
            $programKeahlian = ProgramKeahlian::query()->firstOrCreate(
                ['code' => $program['code']],
                [
                    'name' => $program['name'],
                    'duration_years' => $program['duration_years'],
                    'is_active' => true,
                ]
            );

            foreach ([10, 11, 12] as $gradeLevel) {
                foreach (['A', 'B'] as $label) {
                    $this->seedClassRoom($academicYear, $programKeahlian, $gradeLevel, $label);
                }
            }
        }
    }

    protected function seedClassRoom(AcademicYear $academicYear, ProgramKeahlian $programKeahlian, int $gradeLevel, string $label): void
    {
        $classRoom = ClassRoom::query()->firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'program_keahlian_id' => $programKeahlian->id,
                'grade_level' => $gradeLevel,
                'rombel_label' => $label,
            ],
            [
                'capacity' => 36,
                'is_active' => true,
            ]
        );

        if ($classRoom->classRoomTeachers()->whereNull('ended_at')->doesntExist()) {
            $teacher = Teacher::query()->inRandomOrder()->first()
                ?? Teacher::factory()->create();

            ClassRoomTeacher::query()->create([
                'class_room_id' => $classRoom->id,
                'teacher_id' => $teacher->id,
                'started_at' => $academicYear->start_date ?? now()->startOfYear()->toDateString(),
            ]);
        }

        if ($classRoom->activeStudents()->count() > 0) {
            return;
        }

        Student::factory()
            ->count(20)
            ->create()
            ->each(function (Student $student) use ($classRoom, $academicYear) {

                $joinedAt = $academicYear->start_date ?? now()->startOfYear()->toDateString();

                $classRoom->classRoomStudents()->create([
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'joined_at' => $joinedAt,
                    'status' => ClassRoomStudentStatusEnum::AKTIF->value,
                ]);
            });
    }
}
