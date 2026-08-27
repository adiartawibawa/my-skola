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
        Schema::create('subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            // NULL = mapel umum (berlaku untuk semua program keahlian,
            // mis. Matematika, Bahasa Indonesia). Diisi = mapel
            // kejuruan, hanya bisa dijadwalkan di kelas program
            // keahlian tersebut (lihat Schedule::validateSubjectMatchesProgram()).
            $table->foreignUuid('program_keahlian_id')
                ->nullable()
                ->constrained('program_keahlians')
                ->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
