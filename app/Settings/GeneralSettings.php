<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $school_name;

    public ?string $tagline;

    public ?string $address;

    public ?string $email;

    public ?string $phone;

    public ?string $founded_year;

    public ?string $service_hours_weekday;

    public ?string $service_hours_weekend;

    public ?string $logo;

    public ?string $favicon;

    public ?string $instagram_url;

    public ?string $youtube_url;

    public ?string $facebook_url;

    public static function group(): string
    {
        return 'general';
    }
}
