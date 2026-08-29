<?php

namespace App\Enums;

enum LinkCategory: string
{
    case AKADEMIK = 'akademik';
    case KESISWAAN = 'kesiswaan';
    case UMUM = 'umum';

    public function label(): string
    {
        return match ($this) {
            self::AKADEMIK => 'Akademik',
            self::KESISWAAN => 'Kesiswaan',
            self::UMUM => 'Umum',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
