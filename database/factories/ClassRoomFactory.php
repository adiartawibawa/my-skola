<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\ProgramKeahlian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassRoom>
 */
class ClassRoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'program_keahlian_id' => ProgramKeahlian::factory(),
            'grade_level' => $this->faker->randomElement([10, 11, 12]),
            'rombel_label' => $this->faker->randomElement(['A', 'B', 'C']),
            'capacity' => 36,
            'is_active' => true,
            'description' => null,
        ];

    }
}
