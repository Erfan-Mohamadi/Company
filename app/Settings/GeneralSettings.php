<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name = 'My Company';

    public ?string $logo_path = null;

    public bool $maintenance_mode = false;

    public array $social_links = [];

    public static function group(): string
    {
        return 'general';
    }
}
