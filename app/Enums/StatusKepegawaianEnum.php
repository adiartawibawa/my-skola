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
    case HONORER = 'Honorer';
    case KONTRAK = 'Kontrak';

    public function label(): string
    {
        return $this->value;
    }
}
