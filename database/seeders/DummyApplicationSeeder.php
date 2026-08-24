<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
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
            'role' => RoleEnum::SCHOOL_ADMIN,
        ]);

        Teacher::factory(50)->create();
    }
}
