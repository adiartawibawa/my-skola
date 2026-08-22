<?php

namespace App\Enums;

/**
 * Pendidikan terakhir guru. Dipakai sebagai cast pada model Teacher
 * dan sebagai aturan validasi di UserImporter (kolom pendidikan_terakhir
 * pada file import hanya boleh berisi salah satu value di bawah).
 */
enum PendidikanEnum: string
{
    case SD = 'SD/Sederajat';
    case SMP = 'SMP/Sederajat';
    case SMA = 'SMA/Sederajat';
    case SMK = 'SMK/Sederajat';
    case D1 = 'D1';
    case D2 = 'D2';
    case D3 = 'D3';
    case D4 = 'D4/Sarjana Terapan';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';
    case PROFESI = 'Pendidikan Profesi';

    public function label(): string
    {
        return $this->value;
    }

    // Tambahan method untuk mendapatkan jenjang
    public function jenjang(): string
    {
        return match ($this) {
            self::SD, self::SMP, self::SMA, self::SMK => 'Dasar/Menengah',
            self::D1, self::D2, self::D3, self::D4 => 'Diploma',
            self::S1, self::S2, self::S3, self::PROFESI => 'Akademik/Profesi',
        };
    }
}
