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
        Schema::create('teachers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nip', 18)->unique()->nullable(); // NIP 18 digit [citation:6]
            $table->string('nuptk', 16)->unique()->nullable(); // NUPTK 16 digit [citation:6]
            $table->string('nik', 16)->unique()->nullable(); // NIK sesuai KTP [citation:6]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
