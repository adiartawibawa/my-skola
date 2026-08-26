<?php

namespace Database\Seeders;

use App\Models\Capability;
use Illuminate\Database\Seeder;

class CapabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $capabilities = [
            [
                'key' => 'blog.write',
                'name' => 'Menulis Blog',
                'description' => 'Boleh membuat dan mengelola post miliknya sendiri (default: draft/pending review).',
            ],
            [
                'key' => 'blog.editor',
                'name' => 'Editor Blog',
                'description' => 'Boleh mengedit, menyetujui, dan mempublish post siapa pun, serta moderasi komentar & taksonomi.',
            ],
        ];

        foreach ($capabilities as $capability) {
            Capability::updateOrCreate(['key' => $capability['key']], $capability);
        }
    }
}
