<?php

namespace App\Enums;

/**
 * Golongan kepegawaian (PNS/PPPK), 16 tingkatan standar I/a - IV/d.
 * Nama case memakai underscore (PHP enum tidak boleh punya "/" di nama
 * case) tapi value tetap format aslinya ("I/a", dst) supaya cocok
 * dengan yang diketik admin/diimport dari file.
 */
enum GolonganEnum: string
{
    case I_A = 'I/a';
    case I_B = 'I/b';
    case I_C = 'I/c';
    case I_D = 'I/d';
    case II_A = 'II/a';
    case II_B = 'II/b';
    case II_C = 'II/c';
    case II_D = 'II/d';
    case III_A = 'III/a';
    case III_B = 'III/b';
    case III_C = 'III/c';
    case III_D = 'III/d';
    case IV_A = 'IV/a';
    case IV_B = 'IV/b';
    case IV_C = 'IV/c';
    case IV_D = 'IV/d';

    public function label(): string
    {
        return $this->value;
    }
}
