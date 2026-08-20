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
        Schema::table('academic_years', function (Blueprint $table) {
            $table->date('mid_semester_ganjil_date')->nullable()->after('end_date');
            $table->date('mid_semester_genap_date')->nullable()->after('mid_semester_ganjil_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn(['mid_semester_ganjil_date', 'mid_semester_genap_date']);
        });
    }
};
