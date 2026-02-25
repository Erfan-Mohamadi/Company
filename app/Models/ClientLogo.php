<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Homepage logo strip — clients, sponsors, and partners displayed as logos only.
 * Not to be confused with App\Models\Partner (About section — full partnership records
 * with contracts, contacts, and dates).
 */
class ClientLogo extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $table = 'client_logos';

    protected $fillable = [
        'name',
        'description',
        'type',
        'website_url',
        'featured',
        'order',
        'status',
    ];

    public $translatable = [
        'name',
        'description',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'order'    => 'integer',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';

    public const TYPE_CLIENT      = 'client';
    public const TYPE_PARTNER     = 'partner';
    public const TYPE_SPONSOR     = 'sponsor';
    public const TYPE_SUPPLIER    = 'supplier';
    public const TYPE_DISTRIBUTOR = 'distributor';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_DRAFT    => __('Draft'),
        ];
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_CLIENT      => __('Client'),
            self::TYPE_PARTNER     => __('Partner'),
            self::TYPE_SPONSOR     => __('Sponsor'),
            self::TYPE_SUPPLIER    => __('Supplier'),
            self::TYPE_DISTRIBUTOR => __('Distributor'),
        ];
    }

    // ────────────────────────────── Scopes ──────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ────────────────────────────── Media ──────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('client_logo')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('client_logo');

        $this->addMediaConversion('optimized')
            ->width(400)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('client_logo');
    }

    // ────────────────────────────── Helpers ──────────────────────────────

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('client_logo') ?: null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('client_logo', 'thumb') ?: null;
    }
}
