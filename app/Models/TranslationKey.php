<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class TranslationKey extends Model
{
    use HasTranslations, LogsActivity;

    // Spatie Translatable Configuration
    public $translatable = ['value'];

    protected $table = 'lang_website_keys';

    protected $fillable = [
        'key',
        'value',
        'group',
        'message',
    ];

    protected $casts = [
        'message' => 'boolean',
    ];

    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'value', 'group', 'message'])
            ->setDescriptionForEvent(fn(string $eventName) => "TranslationKey {$eventName}");
    }

    // Model Events
    protected static function booted()
    {
        static::created(function () {
            static::clearCaches();
        });

        static::updated(function () {
            static::clearCaches();
        });

        static::deleted(function () {
            static::clearCaches();
        });
    }

    // Translation Methods
    public static function setWebsiteKey(string $language, string $key, string $value): void
    {
        $translationKey = static::where('key', $key)->firstOrFail();
        $translationKey->setTranslation('value', $language, $value);
        $translationKey->save();
    }

    public static function websiteKeyExists(string $key, string $language = null): bool
    {
        $language = $language ?? App::getLocale();
        $keysAndTranslations = static::getKeysAndTranslationsOf($language);

        return isset($keysAndTranslations[$key]);
    }

    public static function getKeysAndTranslations(bool $useFallback = false): array
    {
        return Cache::rememberForever('translation_keys_' . ($useFallback ? 'with_fallback' : 'no_fallback'), function () use ($useFallback) {
            $keys = static::all();
            $keyObjects = [];
            $languageKeys = Language::getLanguageKeys();

            foreach ($languageKeys as $languageKey) {
                $keyObjects[$languageKey] = [];
                foreach ($keys as $key) {
                    $keyObjects[$languageKey][$key->key] = $key->getTranslation('value', $languageKey, $useFallback);
                }
            }

            return $keyObjects;
        });
    }

    public static function getKeysAndTranslationsOf(string $language = null): array
    {
        $language = $language ?? App::getLocale();

        return Cache::rememberForever('translation_keys_' . $language, function () use ($language) {
            $keys = static::all();
            $keyObjects = [];

            foreach ($keys as $key) {
                $keyObjects[$key->key] = $key->getTranslation('value', $language);
            }

            return $keyObjects;
        });
    }

    public static function clearCaches(): void
    {
        Cache::forget('translation_keys_with_fallback');
        Cache::forget('translation_keys_no_fallback');

        foreach (Language::getLanguageKeys() as $languageKey) {
            Cache::forget('translation_keys_' . $languageKey);
        }
    }

    // Helper Methods
    public function getTranslationForLanguage(string $language): ?string
    {
        return $this->getTranslation('value', $language, false);
    }

    public function hasTranslationForLanguage(string $language): bool
    {
        return !empty($this->getTranslationForLanguage($language));
    }

    public function getAllTranslations(): array
    {
        return $this->getTranslations('value');
    }
}
