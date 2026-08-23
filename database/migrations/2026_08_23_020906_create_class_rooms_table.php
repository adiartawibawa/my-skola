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
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignUuid('program_keahlian_id')
                ->constrained('program_keahlians')
                ->restrictOnDelete();

            // 10/11/12 (X/XI/XII), 13 disediakan untuk program keahlian
            // 4 tahun. Konversi ke romawi ditangani di accessor model.
            $table->unsignedTinyInteger('grade_level');

            // Label paralel: "A", "B", "1", "Pagi", dst — string bebas
            // supaya penamaan rombel tetap fleksibel/dinamis.
            $table->string('rombel_label');

            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Cegah kombinasi tahun ajaran + jurusan + tingkat + label
            // rombel yang sama persis, mis. dua "X TKJ A" di tahun yang sama.
            $table->unique(
                ['academic_year_id', 'program_keahlian_id', 'grade_level', 'rombel_label'],
                'class_rooms_unique_combination'
            );

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
