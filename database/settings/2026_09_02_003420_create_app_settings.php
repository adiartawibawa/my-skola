<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.timezone', 'Asia/Makassar'); // WITA
        $this->migrator->add('app.locale', 'id');
        $this->migrator->add('app.date_format', 'd M Y');
        $this->migrator->add('app.maintenance_mode', false);
        $this->migrator->add('app.maintenance_message', 'Situs sedang dalam pemeliharaan. Silakan kembali beberapa saat lagi.');
    }
};
