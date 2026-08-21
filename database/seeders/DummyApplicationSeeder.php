<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator Sekolah',
            'email' => 'admin@skola.com',
            'password' => Hash::make('password'),
            'role' => RoleEnum::ADMIN->value,
        ]);

        // // 35 Teacher User
        // User::factory(35)->create([
        //     'role' => RoleEnum::TEACHER->value,
        // ])->each(function ($user) {
        //     Teacher::factory()->create([
        //         'user_id' => $user->id,
        //     ]);
        // });

        // // 250 Student User
        // User::factory(250)->create([
        //     'role' => RoleEnum::STUDENT->value,
        // ])->each(function ($user) {
        //     Student::factory()->create([
        //         'user_id' => $user->id,
        //     ]);
        // });
    }
}
