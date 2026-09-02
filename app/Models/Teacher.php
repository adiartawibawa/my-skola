<?php

namespace App\Models;

use App\Enums\GolonganEnum;
use App\Enums\PendidikanEnum;
use App\Enums\StatusKepegawaianEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'user_id',
    'nip',
    'nuptk',
    'nik',
    'status_kepegawaian',
    'bidang_studi',
    'golongan',
    'tanggal_masuk',
    'pendidikan_terakhir',
)]
class Teacher extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'status_kepegawaian' => StatusKepegawaianEnum::class,
            'golongan' => GolonganEnum::class,
            'tanggal_masuk' => 'date',
            'pendidikan_terakhir' => PendidikanEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Riwayat lengkap penugasan wali kelas — termasuk yang sudah
     * berakhir (ended_at terisi).
     */
    public function classRoomTeacherHistories(): HasMany
    {
        return $this->hasMany(ClassRoomTeacher::class);
    }

    /**
     * Kelas yang saat ini diampu sebagai wali kelas (jika ada).
     */
    public function currentHomeroomClass(): ?ClassRoom
    {
        return $this->classRoomTeacherHistories()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first()
            ?->classRoom;
    }

    /**
     * Riwayat lengkap penugasan sebagai Kepala Program Keahlian —
     * termasuk yang sudah berakhir.
     */
    public function programKeahlianHeadHistories(): HasMany
    {
        return $this->hasMany(ProgramKeahlianHead::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Program Keahlian yang saat ini dia pimpin sebagai kaprodi (jika
     * ada).
     */
    public function currentHeadOfProgramKeahlian(): ?ProgramKeahlian
    {
        return $this->programKeahlianHeadHistories()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first()
            ?->programKeahlian;
    }

    /**
     * Satu-satunya titik pengecekan "apakah guru ini kaprodi program
     * X" — dipakai di semua Policy & Resource::getEloquentQuery() yang
     * perlu memberi akses lebih luas untuk kaprodi (ClassRoom,
     * ClassRoomTeacher, ClassRoomStudent, Schedule, Student). Kalau
     * definisi "aktif sebagai kaprodi" berubah nanti, cukup diubah di
     * satu tempat ini.
     */
    public function isHeadOfProgramKeahlian(?string $programKeahlianId): bool
    {
        if (! $programKeahlianId) {
            return false;
        }

        return $this->currentHeadOfProgramKeahlian()?->id === $programKeahlianId;
    }

    /**
     * Accessor untuk mengambil atribut 'name' dari relasi User
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user?->name
        );
    }
}
