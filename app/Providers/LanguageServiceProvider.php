<?php

namespace App\Providers;

use App\Models\Language;
use App\Services\CustomTranslator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;

class LanguageServiceProvider extends ServiceProvider
{
    public static string $defaultLanguage = 'fa';

    public function register(): void
    {
        // Register custom translator
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
        });

        $this->app->singleton('translator', function ($app) {
            $loader = $app->make('translation.loader');
            $locale = $app->getLocale();

            return new CustomTranslator($loader, $locale);
        });
    }

    public function boot(): void
    {
        // Set locale from request header
        if (!$this->app->runningInConsole()) {
            $localeFromRequest = request()->header('Accept-Language');

            if ($localeFromRequest && Language::languageExists($localeFromRequest)) {
                $this->app->setLocale($localeFromRequest);
            } else {
                $this->app->setLocale(static::$defaultLanguage);
            }
        }

        // Set Spatie Translatable locales from database
        try {
            $locales = Language::getLanguageKeys();
            config(['translatable.locales' => $locales]);
        } catch (\Exception $e) {
            // Database not available yet (during migration)
            config(['translatable.locales' => ['fa', 'en']]);
        }

        // Register main language singleton
        $this->app->singleton('main_lang', function () {
            try {
                $lang = Language::query()->where('name', Language::MAIN_LANG)->first();
                return $lang ? $lang->name : static::$defaultLanguage;
            } catch (\Exception $e) {
                return static::$defaultLanguage;
            }
        });
    }
}
