<?php

namespace App\Enums;

enum DayOfWeekEnum: string
{
    case SENIN = 'senin';
    case SELASA = 'selasa';
    case RABU = 'rabu';
    case KAMIS = 'kamis';
    case JUMAT = 'jumat';
    case SABTU = 'sabtu';
    case MINGGU = 'minggu';

    public function label(): string
    {
        return match ($this) {
            self::SENIN => 'Senin',
            self::SELASA => 'Selasa',
            self::RABU => 'Rabu',
            self::KAMIS => 'Kamis',
            self::JUMAT => 'Jumat',
            self::SABTU => 'Sabtu',
            self::MINGGU => 'Minggu',
        };
    }

    /**
     * Urutan tampilan Senin-Minggu (bukan urutan alfabet/enum default),
     * dipakai untuk sortir jadwal dalam satu minggu.
     */
    public function order(): int
    {
        return match ($this) {
            self::SENIN => 1,
            self::SELASA => 2,
            self::RABU => 3,
            self::KAMIS => 4,
            self::JUMAT => 5,
            self::SABTU => 6,
            self::MINGGU => 7,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->sortBy(fn (self $day) => $day->order())
            ->mapWithKeys(fn (self $day) => [$day->value => $day->label()])
            ->toArray();
    }
}
