<?php

namespace App\Enums;

enum ClassRoomStudentStatusEnum: string
{
    case AKTIF = 'aktif';
    case LULUS = 'lulus';
    case KELUAR = 'keluar';
    case PINDAH_SEKOLAH = 'pindah_sekolah';
    case TIDAK_NAIK = 'tidak_naik';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::LULUS => 'Lulus',
            self::KELUAR => 'Keluar',
            self::PINDAH_SEKOLAH => 'Pindah Sekolah',
            self::TIDAK_NAIK => 'Tidak Naik Kelas',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AKTIF => 'success',
            self::LULUS => 'info',
            self::KELUAR, self::PINDAH_SEKOLAH => 'danger',
            self::TIDAK_NAIK => 'warning',
        };
    }

    /**
     * Status selain Aktif menandakan siswa sudah tidak lagi aktif di
     * kelas tersebut — wajib mengisi left_at (lihat
     * ClassRoomStudent::validateExitStatusHasLeftAt()).
     */
    public function isExitStatus(): bool
    {
        return $this !== self::AKTIF;
    }
}
