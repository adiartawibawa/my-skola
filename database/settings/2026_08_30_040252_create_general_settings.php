<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.school_name', config('app.name'));
        $this->migrator->add('general.tagline', 'Sekolah Menengah Kejuruan');
        $this->migrator->add('general.address', '');
        $this->migrator->add('general.email', '');
        $this->migrator->add('general.phone', '');
        $this->migrator->add('general.founded_year', '1998');
        $this->migrator->add('general.service_hours_weekday', '07.00 - 15.00 WITA');
        $this->migrator->add('general.service_hours_weekend', 'Tutup');
        $this->migrator->add('general.logo', null);
        $this->migrator->add('general.favicon', null);
        $this->migrator->add('general.instagram_url', null);
        $this->migrator->add('general.youtube_url', null);
        $this->migrator->add('general.facebook_url', null);
    }
};
