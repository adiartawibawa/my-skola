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

            // Dipakai oleh validateNoOverlap() dan scopeCurrent().
            $table->index(['start_date', 'end_date']);
        });

        Schema::table('academic_calendars', function (Blueprint $table) {
            // Menunjang filter tahun ajaran + rentang tanggal sekaligus,
            // dipakai oleh scopeCurrent/scopeUpcoming dan nanti oleh
            // getEvents() di CalendarWidget (filter per tahun ajaran + range).
            $table->index(['academic_year_id', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropIndex(['start_date', 'end_date']);
            $table->dropColumn(['mid_semester_ganjil_date', 'mid_semester_genap_date']);
        });

        Schema::table('academic_calendars', function (Blueprint $table) {
            $table->dropIndex(['academic_year_id', 'event_date']);
        });
    }
};
