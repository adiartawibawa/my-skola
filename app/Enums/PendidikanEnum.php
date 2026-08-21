<?php

namespace App\Enums;

/**
 * Pendidikan terakhir guru. Dipakai sebagai cast pada model Teacher
 * dan sebagai aturan validasi di UserImporter (kolom pendidikan_terakhir
 * pada file import hanya boleh berisi salah satu value di bawah).
 */
enum PendidikanEnum: string
{
    case SMA = 'SMA';
    case D3 = 'D3';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';

    public function label(): string
    {
        return $this->value;
    }
}
