<?php

namespace App\Enums\Enums;

enum SemesterEnum: string
{
    case GANJIL = 'Ganjil';
    case GENAP = 'Genap';

    public function label(): string
    {
        return $this->value;
    }
}
