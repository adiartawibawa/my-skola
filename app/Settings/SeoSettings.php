<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public string $default_meta_title;

    public ?string $default_meta_description;

    public ?string $default_og_image;

    public ?string $google_search_console_verification;

    public ?string $google_analytics_id;

    public ?string $twitter_username;

    public bool $indexable;

    public static function group(): string
    {
        return 'seo';
    }
}
