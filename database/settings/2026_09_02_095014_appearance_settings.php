<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('appearance.primary', '#6B1220');
        $this->migrator->add('appearance.primary_dark', '#4A0D17');
        $this->migrator->add('appearance.primary_light', '#8C1F2E');
        $this->migrator->add('appearance.accent', '#C89B3C');
        $this->migrator->add('appearance.accent_light', '#E4C878');
        $this->migrator->add('appearance.paper', '#FBF6EE');
        $this->migrator->add('appearance.ink', '#241512');
    }
};
