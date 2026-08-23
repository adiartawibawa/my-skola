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
        Schema::create('class_room_teachers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('class_room_id')
                ->constrained('class_rooms')
                ->restrictOnDelete();

            $table->foreignUuid('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            $table->date('started_at');
            // NULL = masih menjabat sebagai wali kelas.
            $table->date('ended_at')->nullable();
            $table->string('reason')->nullable();

            $table->timestamps();

            $table->index(['class_room_id', 'ended_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_room_teachers');
    }
};
