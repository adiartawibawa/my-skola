<?php

namespace App\Enums;

enum GuardianRelationshipType: string
{
    case AYAH = 'ayah';
    case IBU = 'ibu';
    case WALI = 'wali';

    public function label(): string
    {
        return match ($this) {
            self::AYAH => 'Ayah',
            self::IBU => 'Ibu',
            self::WALI => 'Wali',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
