<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class MissionVision extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'header',
        'vision_title',
        'vision_text',
        'mission_title',
        'mission_text',
        'short_description',
        'video_url',
        'status',
    ];

    public $translatable = [
        'header',
        'vision_title',
        'vision_text',
        'mission_title',
        'mission_text',
        'short_description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            // → NO maxFiles() or multiple() here anymore
            // Filament's SpatieMediaLibraryFileUpload::maxFiles(5) already enforces this
            ->useDisk('public');

        $this->addMediaCollection('video')
            ->singleFile()
            ->useDisk('public');
    }

    // Optional – keep conversions if you use them
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10)
            ->performOnCollections('images');

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->performOnCollections('images');
    }
}
