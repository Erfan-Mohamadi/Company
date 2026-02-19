<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Partner extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'partner_name', 'description',
        'website_url', 'partnership_type',
        'start_date', 'end_date',
        'contact_person', 'email', 'phone',
        'featured', 'order', 'status',
    ];

    public $translatable = ['partner_name', 'description'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'featured' => 'boolean', 'order' => 'integer'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('logo');
    }
}
