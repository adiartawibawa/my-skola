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
        Schema::create('class_room_students', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('class_room_id')
                ->constrained('class_rooms')
                ->restrictOnDelete();

            $table->foreignUuid('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            // Denormalisasi dari class_room.academic_year_id — lihat
            // ClassRoomStudent::syncAcademicYear(). Dipasang di sini
            // supaya unique constraint di bawah bisa menegakkan aturan
            // "1 siswa hanya 1 kelas per tahun ajaran" di level DB.
            $table->foreignUuid('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->string('status')->default('aktif');

            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_room_students');
    }
};
