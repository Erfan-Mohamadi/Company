<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Syntax: $this->migrator->add('group.property', default_value);
        $this->migrator->add('home.hero_title', 'Welcome to Our Company');
        $this->migrator->add('home.hero_subtitle', 'We deliver excellence');
        $this->migrator->add('home.hero_image', null);
        $this->migrator->add('home.hero_cta_text', 'Get Started');
        $this->migrator->add('home.hero_cta_link', null);
        $this->migrator->add('home.features', []);
        $this->migrator->add('home.show_statistics', true);
        $this->migrator->add('home.statistics', []);
    }

    public function down(): void
    {
        $this->migrator->delete('home.hero_title');
        $this->migrator->delete('home.hero_subtitle');
        $this->migrator->delete('home.hero_image');
        $this->migrator->delete('home.hero_cta_text');
        $this->migrator->delete('home.hero_cta_link');
        $this->migrator->delete('home.features');
        $this->migrator->delete('home.show_statistics');
        $this->migrator->delete('home.statistics');
    }
};
