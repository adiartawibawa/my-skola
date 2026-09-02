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
        Schema::create('program_keahlian_heads', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('program_keahlian_id')
                ->constrained('program_keahlians')
                ->restrictOnDelete();

            $table->foreignUuid('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            $table->date('started_at');
            // NULL = masih menjabat sebagai kaprodi.
            $table->date('ended_at')->nullable();
            $table->string('reason')->nullable();

            $table->timestamps();

            $table->index(['program_keahlian_id', 'ended_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_keahlian_heads');
    }
};
