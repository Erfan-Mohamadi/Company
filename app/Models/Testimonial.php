<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'customer_name',
        'customer_position',
        'customer_company',
        'testimonial_text',
        'rating',
        'video_url',
        'featured',
        'order',
        'status',
    ];

    public $translatable = [
        'customer_name',
        'customer_position',
        'customer_company',
        'testimonial_text',
    ];

    protected $casts = [
        'rating'   => 'integer',
        'featured' => 'boolean',
        'order'    => 'integer',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';
    public const STATUS_PENDING  = 'pending';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_DRAFT    => __('Draft'),
            self::STATUS_PENDING  => __('Pending Approval'),
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

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }

    // ────────────────────────────── Media ──────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('testimonial_avatars')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->performOnCollections('testimonial_avatars');

        $this->addMediaConversion('avatar')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('testimonial_avatars');
    }

    // ────────────────────────────── Helpers ──────────────────────────────

    public function approve(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('testimonial_avatars', 'avatar') ?: null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('testimonial_avatars', 'thumb') ?: null;
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('⭐', max(1, min(5, $this->rating)));
    }
}
