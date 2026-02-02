<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'My Company');
        $this->migrator->add('general.logo_path', null);
        $this->migrator->add('general.maintenance_mode', false);
        $this->migrator->add('general.social_links', []);
    }
};
