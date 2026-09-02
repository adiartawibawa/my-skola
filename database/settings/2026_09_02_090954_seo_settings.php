<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seo.default_meta_title', config('app.name'));
        $this->migrator->add('seo.default_meta_description', 'Sistem informasi akademik sekaligus media resmi sekolah.');
        $this->migrator->add('seo.default_og_image', null);
        $this->migrator->add('seo.google_search_console_verification', null);
        $this->migrator->add('seo.google_analytics_id', null);
        $this->migrator->add('seo.twitter_username', null);
        $this->migrator->add('seo.indexable', true);
    }
};
