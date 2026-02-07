<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Language Switch configuration
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['fa', 'en'])
                ->visible(outsidePanels: true)
                ->displayLocale('fa')           // show Persian names by default
                ->circular()
                // If you want flags/icons later, uncomment and install required assets:
                // ->flags([
                //     'fa' => 'fi fi-ir',
                //     'en' => 'fi fi-gb',
                // ])
                // ->circular() is already there — good for rounded look
            ;
        });

        // Safe Blade directive for generating Filament resource URLs
        Blade::directive('settingsUrl', function ($expression) {
            // $expression is the arguments passed: 'group', ['group' => $groupKey]
            return "<?php echo \App\Filament\Resources\Settings\SettingResource::getUrl({$expression}); ?>";
        });
    }
}
