<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mail.mailer', 'smtp');
        $this->migrator->add('mail.host', 'smtp.mailtrap.io');
        $this->migrator->add('mail.port', 587);
        $this->migrator->add('mail.username', '');
        $this->migrator->addEncrypted('mail.password', '');
        $this->migrator->add('mail.encryption', 'tls');
        $this->migrator->add('mail.from_address', 'noreply@example.com');
        $this->migrator->add('mail.from_name', config('app.name'));
    }
};
