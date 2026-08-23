<?php

namespace Database\Factories;

use App\Enums\ClassRoomStudentStatusEnum;
use App\Models\ClassRoom;
use App\Models\ClassRoomStudent;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassRoomStudent>
 */
class ClassRoomStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // academic_year_id sengaja tidak diisi di sini — otomatis
            // disinkronkan dari class_room-nya oleh
            // ClassRoomStudent::syncAcademicYear() saat disimpan.
            'class_room_id' => ClassRoom::factory(),
            'student_id' => Student::factory(),
            'joined_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'left_at' => null,
            'status' => ClassRoomStudentStatusEnum::AKTIF->value,
        ];
    }
}
