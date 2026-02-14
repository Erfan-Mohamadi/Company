<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Language extends Model implements HasMedia
{
    use LogsActivity, InteractsWithMedia;

    const MAIN_LANG = 'fa';

    protected $fillable = [
        'name',
        'label',
        'is_rtl',
    ];

    protected $casts = [
        'is_rtl' => 'boolean',
    ];

    protected $appends = ['flag'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'label', 'is_rtl'])
            ->setDescriptionForEvent(fn(string $eventName) => "Language {$eventName}");
    }

    // Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('flag')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function getFlagAttribute(): ?string
    {
        $media = $this->getFirstMedia('flag');
        return $media ? $media->getUrl() : null;
    }

    // Static Methods for Filament
    public static function getAllLanguages()
    {
        return Cache::rememberForever('all_languages', function () {
            return static::query()->orderBy('name', 'desc')->get();
        });
    }

    public static function getOtherLanguages()
    {
        return Cache::rememberForever('other_languages', function () {
            return static::query()
                ->where('name', '!=', static::MAIN_LANG)
                ->orderBy('name', 'desc')
                ->get();
        });
    }

    public static function getLanguageKeys(): array
    {
        return Cache::rememberForever('language_keys', function () {
            return static::pluck('name')->toArray();
        });
    }

    public static function languageExists(string $language): bool
    {
        return in_array($language, static::getLanguageKeys());
    }

    // Get locales for Filament Translatable Tabs
    public static function getLocales(): array
    {
        return Cache::rememberForever('locales_for_filament', function () {
            return static::pluck('label', 'name')->toArray();
        });
    }

    // Model Events
    protected static function booted()
    {
        static::created(function () {
            static::clearAllCaches();
        });

        static::updated(function () {
            static::clearAllCaches();
        });

        static::deleting(function (Language $language) {
            if (!$language->isDeletable()) {
                throw new \Exception('Cannot delete the main language.');
            }
        });

        static::deleted(function () {
            static::clearAllCaches();
        });
    }

    // Helper Methods
    public function isDeletable(): bool
    {
        return $this->name !== static::MAIN_LANG;
    }

    public static function clearAllCaches(): void
    {
        Cache::forget('all_languages');
        Cache::forget('other_languages');
        Cache::forget('language_keys');
        Cache::forget('locales_for_filament');
    }

    public function getIsMainLanguageAttribute(): bool
    {
        return $this->name === static::MAIN_LANG;
    }
}
