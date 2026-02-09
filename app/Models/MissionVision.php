<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MissionVision extends Model implements HasMedia
{
    use InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->multiple()
            ->maxFiles(5);

        $this->addMediaCollection('video')
            ->singleFile();
    }
}
