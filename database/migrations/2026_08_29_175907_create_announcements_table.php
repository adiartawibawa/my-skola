<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('body');
            // true = tayang untuk semua pengguna, target_roles/
            // classRooms/programKeahlians di bawah diabaikan.
            $table->boolean('is_for_all')->default(false);
            $table->boolean('is_pinned')->default(false);
            // NULL publish_at = langsung tayang. NULL expires_at =
            // tidak pernah kedaluwarsa.
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('announcement_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('announcement_id')->constrained('announcements')->cascadeOnDelete();
            // Value dari App\Enums\RoleEnum — bukan FK, karena role
            // bukan tabel tersendiri.
            $table->string('role');
            $table->timestamps();
            $table->unique(['announcement_id', 'role']);
        });

        Schema::create('announcement_class_room', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignUuid('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['announcement_id', 'class_room_id']);
        });

        Schema::create('announcement_program_keahlian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignUuid('program_keahlian_id')->constrained('program_keahlians')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(
                ['announcement_id', 'program_keahlian_id'],
                'announcement_program_keahlian_unique',
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_program_keahlian');
        Schema::dropIfExists('announcement_class_room');
        Schema::dropIfExists('announcement_roles');
        Schema::dropIfExists('announcements');
    }
};
