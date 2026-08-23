<?php

namespace Database\Factories;

use App\Models\ProgramKeahlian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramKeahlian>
 */
class ProgramKeahlianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $programs = [
            ['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan', 'duration_years' => 3],
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'duration_years' => 3],
            ['code' => 'OTO', 'name' => 'Teknik Otomotif', 'duration_years' => 3],
            ['code' => 'AKL', 'name' => 'Akuntansi dan Keuangan Lembaga', 'duration_years' => 3],
            ['code' => 'NKPI', 'name' => 'Nautika Kapal Penangkap Ikan', 'duration_years' => 4],
        ];

        $program = $this->faker->unique()->randomElement($programs);

        return [
            'code' => $program['code'],
            'name' => $program['name'],
            'duration_years' => $program['duration_years'],
            'is_active' => true,
            'description' => null,
        ];

    }
}
