<?php

namespace App\Enums\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case USER = 'user';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->name])
            ->toArray();
    }
}
