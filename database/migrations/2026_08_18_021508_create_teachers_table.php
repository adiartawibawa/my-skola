<?php

use App\Enums\GolonganEnum;
use App\Enums\PendidikanEnum;
use App\Enums\StatusKepegawaianEnum;
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
            $table->string('nip', 18)->unique()->nullable(); // NIP 18 digit
            $table->string('nuptk', 16)->unique()->nullable(); // NUPTK 16 digit
            $table->string('nik', 16)->unique()->nullable(); // NIK sesuai KTP
            $table->enum('status_kepegawaian', array_column(StatusKepegawaianEnum::cases(), 'value'))->nullable();
            $table->string('bidang_studi')->nullable();
            $table->enum('golongan', array_column(GolonganEnum::cases(), 'value'))->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->enum('pendidikan_terakhir', array_column(PendidikanEnum::cases(), 'value'))->nullable();

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
