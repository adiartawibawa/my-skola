<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('notification.notify_on_contact_message', true);
        $this->migrator->add('notification.notify_email', null);
    }
};
