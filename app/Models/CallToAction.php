<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class CallToAction extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $table = 'call_to_actions';

    protected $fillable = [
        'title',
        'description',
        'button_text',
        'button_link',
        'button_style',
        'background_color',
        'overlay_opacity',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'description',
        'button_text',
    ];

    protected $casts = [
        'overlay_opacity' => 'integer',
        'order'           => 'integer',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';

    public const BUTTON_STYLE_PRIMARY   = 'primary';
    public const BUTTON_STYLE_SECONDARY = 'secondary';
    public const BUTTON_STYLE_OUTLINE   = 'outline';
    public const BUTTON_STYLE_WHITE     = 'white';

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
            self::BUTTON_STYLE_WHITE     => __('White'),
        ];
    }

    // ────────────────────────────── Scopes ──────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ────────────────────────────── Media ──────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cta_backgrounds')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(225)
            ->sharpen(10)
            ->performOnCollections('cta_backgrounds');

        $this->addMediaConversion('optimized')
            ->width(1920)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('cta_backgrounds');
    }

    // ────────────────────────────── Helpers ──────────────────────────────

    public function hasBackgroundImage(): bool
    {
        return $this->getFirstMedia('cta_backgrounds') !== null;
    }

    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cta_backgrounds') ?: null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cta_backgrounds', 'thumb') ?: null;
    }
}
