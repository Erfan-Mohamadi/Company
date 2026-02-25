<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Slider extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'link_text',
        'link_url',
        'button_style',
        'video_url',
        'animation_type',
        'display_duration',
        'start_date',
        'end_date',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'subtitle',
        'description',
        'link_text',
    ];

    protected $casts = [
        'display_duration' => 'integer',
        'order'            => 'integer',
        'start_date'       => 'datetime',
        'end_date'         => 'datetime',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';

    public const BUTTON_STYLE_PRIMARY   = 'primary';
    public const BUTTON_STYLE_SECONDARY = 'secondary';
    public const BUTTON_STYLE_OUTLINE   = 'outline';
    public const BUTTON_STYLE_GHOST     = 'ghost';

    public const ANIMATION_FADE  = 'fade';
    public const ANIMATION_SLIDE = 'slide';
    public const ANIMATION_ZOOM  = 'zoom';
    public const ANIMATION_NONE  = 'none';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_DRAFT    => __('Draft'),
        ];
    }

    public static function getButtonStyles(): array
    {
        return [
            self::BUTTON_STYLE_PRIMARY   => __('Primary'),
            self::BUTTON_STYLE_SECONDARY => __('Secondary'),
            self::BUTTON_STYLE_OUTLINE   => __('Outline'),
            self::BUTTON_STYLE_GHOST     => __('Ghost'),
        ];
    }

    public static function getAnimationTypes(): array
    {
        return [
            self::ANIMATION_FADE  => __('Fade'),
            self::ANIMATION_SLIDE => __('Slide'),
            self::ANIMATION_ZOOM  => __('Zoom'),
            self::ANIMATION_NONE  => __('None'),
        ];
    }

    // ────────────────────────────── Scopes ──────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ────────────────────────────── Media ──────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slider_media')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(225)
            ->sharpen(10)
            ->performOnCollections('slider_media');

        $this->addMediaConversion('optimized')
            ->width(1920)
            ->height(1080)
            ->sharpen(10)
            ->performOnCollections('slider_media');
    }

    // ────────────────────────────── Helpers ──────────────────────────────

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }

        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }

        return true;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('slider_media') ?: null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('slider_media', 'thumb') ?: null;
    }
}
