<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
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
            'nip' => $this->faker->unique()->numerify('##################'), // 18 digit;
            'nuptk' => $this->faker->unique()->numerify('################'), // 16 digit;
            'nik' => $this->faker->unique()->numerify('################'), // 16 digit;
        ];
    }
}
