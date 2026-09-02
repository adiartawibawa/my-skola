<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppearanceSettings extends Settings
{
    public string $primary;

    public string $primary_dark;

    public string $primary_light;

    public string $accent;

    public string $accent_light;

    public string $paper;

    public string $ink;

    public static function group(): string
    {
        return 'appearance';
    }
}
