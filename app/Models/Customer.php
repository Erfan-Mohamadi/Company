<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Customer extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'name', 'testimonial_text', 'project_description',
        'website_url', 'industry', 'country',
        'author_name', 'author_position',
        'featured', 'order', 'status',
    ];

    public $translatable = ['name', 'testimonial_text', 'project_description', 'author_name', 'author_position'];

    protected $casts = ['featured' => 'boolean', 'order' => 'integer'];

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
