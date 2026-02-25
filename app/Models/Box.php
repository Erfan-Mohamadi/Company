<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Box extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'header',
        'description',
        'link_url',
        'box_type',
        'icon',
        'background_color',
        'text_color',
        'order',
        'status',
    ];

    public $translatable = [
        'header',
        'description',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';

    public const TYPE_ICON       = 'icon';
    public const TYPE_IMAGE      = 'image';
    public const TYPE_ICON_IMAGE = 'icon+image';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_DRAFT    => __('Draft'),
        ];
    }

    public static function getBoxTypes(): array
    {
        return [
            self::TYPE_ICON       => __('Icon Only'),
            self::TYPE_IMAGE      => __('Image Only'),
            self::TYPE_ICON_IMAGE => __('Icon + Image'),
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
        $this->addMediaCollection('box_images')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('box_images');

        $this->addMediaConversion('optimized')
            ->width(800)
            ->height(800)
            ->sharpen(10)
            ->performOnCollections('box_images');
    }

    // ────────────────────────────── Helpers ──────────────────────────────

    public function hasIcon(): bool
    {
        return in_array($this->box_type, [self::TYPE_ICON, self::TYPE_ICON_IMAGE])
            && filled($this->icon);
    }

    public function hasImage(): bool
    {
        return in_array($this->box_type, [self::TYPE_IMAGE, self::TYPE_ICON_IMAGE])
            && $this->getFirstMedia('box_images') !== null;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('box_images') ?: null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('box_images', 'thumb') ?: null;
    }
}
