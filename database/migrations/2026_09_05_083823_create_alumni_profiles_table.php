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
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Terisi OTOMATIS untuk jalur digital (observer), NULL untuk
            // alumni legacy sampai TU berhasil mencocokkan data arsip.
            $table->foreignUuid('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignUuid('program_keahlian_id')->nullable()->constrained('program_keahlians')->nullOnDelete();

            $table->unsignedSmallInteger('tahun_lulus')->nullable();
            $table->string('nis_klaim')->nullable()->comment('NIS yang diklaim sendiri saat registrasi legacy, belum tentu valid');

            $table->boolean('is_verified')->default(false);
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
