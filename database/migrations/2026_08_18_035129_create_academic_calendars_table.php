<?php

use App\Enums\EventType;
use App\Enums\SemesterEnum;
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
        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('event_name');
            $table->date('event_date');
            $table->date('event_end_date')->nullable(); // Untuk event multi-hari
            $table->enum('event_type', array_column(EventType::cases(), 'value'));
            $table->enum('semester', array_column(SemesterEnum::cases(), 'value'))->nullable();
            $table->boolean('is_national_holiday')->default(false);
            $table->boolean('is_school_holiday')->default(false);
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // Untuk UI kalender
            $table->timestamps();

            // Index untuk optimasi query
            $table->index('event_date');
            $table->index('event_type');
            $table->index('semester');
            $table->index(['academic_year_id', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
    }
};
