<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class CeoMessage extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'title',
        'message_text',
        'ceo_name',
        'ceo_position',
        'video_url',
        'status',
    ];

    public $translatable = [
        'title',
        'message_text',
        'ceo_name',
        'ceo_position',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ceo_image')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('ceo_signature')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('ceo_image');
    }
}
