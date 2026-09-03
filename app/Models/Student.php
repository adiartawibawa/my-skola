<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function guardianLinks(): HasMany
    {
        return $this->hasMany(GuardianStudent::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'guardian_student')
            ->withPivot(['relationship_type', 'verified_at'])
            ->withTimestamps();
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

    /**
     * Program Keahlian siswa ini — dari kelas yang sedang aktif kalau
     * masih terdaftar, atau dari kelas TERAKHIR di riwayat kalau sudah
     * tidak aktif lagi (lulus/keluar/pindah). Sengaja BUKAN kolom
     * tersimpan — selalu diturunkan dari ClassRoom yang pernah/sedang
     * dia ikuti, supaya tidak ada dua sumber kebenaran yang bisa tidak
     * sinkron dengan riwayat kelas sebenarnya.
     */
    public function programKeahlian(): ?ProgramKeahlian
    {
        $classRoomId = $this->currentClassRoom()?->id;

        if (! $classRoomId) {
            // withoutGlobalScopes(): siswa yang sudah tidak aktif
            // (lulus/keluar/pindah) tidak akan ketemu lewat
            // currentClassRoom() — cari kelas TERAKHIR yang pernah dia
            // ikuti dari riwayat, lintas Tahun Akademik.
            $classRoomId = $this->classRoomEnrollments()
                ->withoutGlobalScopes()
                ->orderByDesc('joined_at')
                ->first()
                ?->class_room_id;
        }

        if (! $classRoomId) {
            return null;
        }

        return ClassRoom::query()->withoutGlobalScopes()->find($classRoomId)?->programKeahlian;
    }

    protected function nisnName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nisn.' - '.$this->user?->name,
        );
    }
}
