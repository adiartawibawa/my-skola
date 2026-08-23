<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nis' => $this->faker->unique()->numerify('#####'), // 5 digit
            'nisn' => $this->faker->unique()->numerify('##########'), // 10 digit
        ];
    }
}
