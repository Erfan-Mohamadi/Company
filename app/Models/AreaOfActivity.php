<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class AreaOfActivity extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'meta_description',
        'icon',
        'industries',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'short_description',
        'description',
        'meta_description',
    ];

    protected $casts = [
        'industries' => 'array',
        'order'      => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('image');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('image');
    }
}
