<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $timezone;

    public string $locale;

    public string $date_format;

    public bool $maintenance_mode;

    public ?string $maintenance_message;

    public static function group(): string
    {
        return 'app';
    }
}
