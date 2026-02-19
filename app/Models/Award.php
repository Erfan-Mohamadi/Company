<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Award extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'awarding_body',
        'award_date',
        'category',
        'featured',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'description',
        'awarding_body',
        'category',
    ];

    protected $casts = [
        'award_date' => 'date',
        'featured'   => 'boolean',
        'order'      => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('certificate_file')
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
    }
}
