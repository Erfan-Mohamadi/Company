<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class CompanyHistory extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',        // missing
        'description',  // missing
        'date',
        'achievement_type',
        'icon',
        'order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'order' => 'integer',
    ];

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public $translatable = [
        'title',
        'description',
    ];

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useFallbackUrl(asset('images/placeholder-history.jpg'));
    }

    /**
     * Scope a query to only include published records.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to order by display order and date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')
            ->orderByDesc('date');
    }

    /**
     * Get formatted date for display (Jalali/Gregorian aware).
     *
     * @return string
     */
    public function getFormattedDateAttribute(): string
    {
        if (!$this->date) {
            return '—';
        }

        $isFarsi = app()->isLocale('fa');

        if ($isFarsi && class_exists(\Morilog\Jalali\Jalalian::class)) {
            $jalali = \Morilog\Jalali\Jalalian::fromCarbon($this->date);
            $months = [
                1  => 'فروردین', 2  => 'اردیبهشت', 3  => 'خرداد', 4  => 'تیر',
                5  => 'مرداد',   6  => 'شهریور',   7  => 'مهر',   8  => 'آبان',
                9  => 'آذر',     10 => 'دی',       11 => 'بهمن',  12 => 'اسفند',
            ];
            return $jalali->format('%d ') . ($months[$jalali->getMonth()] ?? '') . $jalali->format(' %Y');
        }

        return $this->date->format('M j, Y');
    }

    /**
     * Get achievement type label.
     *
     * @return string
     */
    public function getAchievementTypeLabelAttribute(): string
    {
        return match ($this->achievement_type) {
            'founding'       => __('Founding'),
            'product_launch' => __('Product Launch'),
            'expansion'      => __('Expansion'),
            'award'          => __('Award'),
            'partnership'    => __('Partnership'),
            default          => __('Other'),
        };
    }
}
