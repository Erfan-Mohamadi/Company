<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class About extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'header',
        'description',
        'founded_year',
        'founder_name',
        'mission',
        'vision',
        'core_values',
        'employees_count',
        'locations_count',
        'clients_count',
        'founder_message',
        'status',
        'extra'
    ];

    protected $casts = [
        'core_values' => 'array',
        'extra' => 'array',
    ];

    /**
     * Convert Jalali year to Gregorian before saving
     */
    public function setFoundedYearAttribute($value)
    {
        if ($value && is_numeric($value)) {
            // If value is in Jalali range (1200-1500), convert to Gregorian
            if ($value >= 1200 && $value <= 1500) {
                $this->attributes['founded_year'] = intval($value) + 621;
            } else {
                $this->attributes['founded_year'] = $value;
            }
        } else {
            $this->attributes['founded_year'] = $value;
        }
    }

    /**
     * Convert Gregorian year to Jalali when retrieving
     */
    public function getFoundedYearAttribute($value)
    {
        if ($value && is_numeric($value)) {
            // If value is in Gregorian range, convert to Jalali for display
            if ($value >= 1800 && $value <= 2200) {
                return intval($value) - 621;
            }
        }
        return $value;
    }

    /**
     * Get the Gregorian year (for display purposes)
     */
    public function getFoundedYearGregorianAttribute()
    {
        $jalaliYear = $this->attributes['founded_year'] ?? null;

        if ($jalaliYear && is_numeric($jalaliYear)) {
            // The database already stores Gregorian, so just return it
            return $jalaliYear;
        }

        return null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');

        $this->addMediaCollection('founder_image')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('video')
            ->singleFile()
            ->useDisk('public');
    }

    /**
     * Register media conversions for different collections
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Optional: Add image conversions/thumbnails if needed
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10)
            ->performOnCollections('images', 'founder_image');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('images');
    }
}
