<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'user_id',
    'nis',
    'nisn',
    'tempat_lahir',
    'tanggal_lahir',
    'nama_ayah',
    'nama_ibu',
    'pekerjaan_orang_tua',
    'alamat_orang_tua',
    'no_telp_orang_tua',
    'is_active',
)]
class Student extends Model
{
    use HasFactory;
    use HasUuids;

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Riwayat lengkap keanggotaan kelas dari tahun ke tahun — termasuk
     * yang sudah berakhir (status selain Aktif).
     */
    public function classRoomEnrollments(): HasMany
    {
        return $this->hasMany(ClassRoomStudent::class);
    }

    /**
     * Kelas siswa pada Tahun Akademik yang sedang aktif.
     */
    public function currentClassRoom(): ?ClassRoom
    {
        return $this->classRoomEnrollments()
            ->whereHas('academicYear', fn ($query) => $query->active())
            ->first()
            ?->classRoom;
    }

    protected function nisnName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nisn.' - '.$this->user?->name,
        );
    }
}
