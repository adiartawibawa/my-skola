<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = $this->faker->unique()->numberBetween(2015, Carbon::now()->year);

        $startDate = Carbon::create($startYear, 7, 1)->format('Y-m-d');
        $endDate = Carbon::create($startYear + 1, 6, 30)->format('Y-m-d');

        return [
            'code' => "{$startYear}/".($startYear + 1),
            'name' => "Tahun Ajaran {$startYear}/".($startYear + 1),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'mid_semester_ganjil_date' => Carbon::create($startYear, 10, 1),
            'mid_semester_genap_date' => Carbon::create($startYear + 1, 3, 1),
            'is_active' => false,
            'description' => null,
        ];

    }

    /**
     * AcademicYear::enforceSingleActive() otomatis menonaktifkan Tahun
     * Akademik lain saat salah satu diset aktif, jadi state ini aman
     * dipakai kapan pun — mis. AcademicYear::factory()->active()->create().
     */
    public function active(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
        ]);
    }
}
