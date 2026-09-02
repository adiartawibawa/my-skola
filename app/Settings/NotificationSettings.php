<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{
    public bool $notify_on_contact_message;

    public ?string $notify_email;

    public static function group(): string
    {
        return 'notification';
    }
}
