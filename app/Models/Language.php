<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('flag')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->performOnCollections('flag');
    }

    public function getFlagAttribute(): ?string
    {
        $media = $this->getFirstMedia('flag');
        return $media ? $media->getUrl() : null;
    }

    // ─── Static Helpers ────────────────────────────────────────────────────────

    public static function getAllLanguages()
    {
        return Cache::rememberForever('all_languages', fn () =>
        static::query()->orderBy('name', 'desc')->get()
        );
    }

    public static function getOtherLanguages()
    {
        return Cache::rememberForever('other_languages', fn () =>
        static::query()
            ->where('name', '!=', static::MAIN_LANG)
            ->orderBy('name', 'desc')
            ->get()
        );
    }

    public static function getLanguageKeys(): array
    {
        return Cache::rememberForever('language_keys', fn () =>
        static::pluck('name')->toArray()
        );
    }

    public static function languageExists(string $code): bool
    {
        return in_array($code, static::getLanguageKeys());
    }

    public static function clearAllCaches(): void
    {
        Cache::forget('all_languages');
        Cache::forget('other_languages');
        Cache::forget('language_keys');
        Cache::forget('locales_for_filament');
    }

    protected static function booted()
    {
        static::saved(fn () => static::clearAllCaches());
        static::deleted(fn () => static::clearAllCaches());
    }
}
