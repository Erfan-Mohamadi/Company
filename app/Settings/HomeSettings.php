<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public string $hero_title = 'Welcome to Our Company';
    public string $hero_subtitle = 'We deliver excellence';
    public ?string $hero_image = null;
    public string $hero_cta_text = 'Get Started';
    public ?string $hero_cta_link = null;
    public array $features = [];
    public bool $show_statistics = true;
    public array $statistics = [];

    public static function group(): string
    {
        return 'home';
    }
}
