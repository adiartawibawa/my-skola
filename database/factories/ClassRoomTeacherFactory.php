<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\ClassRoomTeacher;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassRoomTeacher>
 */
class ClassRoomTeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_room_id' => ClassRoom::factory(),
            'teacher_id' => Teacher::factory(),
            'started_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'ended_at' => null,
            'reason' => null,
        ];
    }
}
