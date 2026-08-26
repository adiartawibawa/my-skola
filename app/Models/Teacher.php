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
     * Accessor untuk mengambil atribut 'name' dari relasi User
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user?->name
        );
    }
}
