<?php

namespace App\Enums;

/**
 * Status kepegawaian guru. Dipakai sebagai cast pada model Teacher
 * dan sebagai aturan validasi di UserImporter (kolom status_kepegawaian
 * pada file import hanya boleh berisi salah satu value di bawah).
 */
enum StatusKepegawaianEnum: string
{
    case PNS = 'PNS';
    case PPPK = 'PPPK';
    case PPPK_PWT = 'PPPK Paruh Waktu';
    case GTT = 'GTT (Guru Tidak Tetap)';
    case HONORER = 'Honorer';
    case KONTRAK = 'Kontrak';
    case SWASTA = 'Guru Swasta';
    case VOLUNTEER = 'Relawan/Volunteer';

    public function label(): string
    {
        return $this->value;
    }

    // Tambahan method untuk kategori status
    public function kategori(): string
    {
        return match ($this) {
            self::PNS, self::PPPK => 'ASN',
            self::PPPK_PWT, self::GTT, self::HONORER, self::KONTRAK => 'Non-ASN',
            self::SWASTA, self::VOLUNTEER => 'Lainnya',
        };
    }

    // Cek apakah status termasuk ASN
    public function isASN(): bool
    {
        return in_array($this, [self::PNS, self::PPPK]);
    }
}
