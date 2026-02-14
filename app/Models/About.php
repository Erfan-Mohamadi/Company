<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class About extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'header',
        'description',
        'founded_date',
        'founder_name',
        'mission',
        'vision',
        'core_values',
        'employees_count',
        'locations_count',
        'clients_count',
        'founder_message',
        'status',
        'extra',
    ];

    public array $translatable = [
        'header',
        'description',
        'founder_name',
        'mission',
        'vision',
        'core_values',
        'founder_message',
        'extra',
    ];

    protected $casts = [
        'core_values'   => 'array',
        'extra'         => 'array',
        'founded_date'  => 'date:Y-m-d',
    ];

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

    public function registerMediaConversions(?Media $media = null): void
    {
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
