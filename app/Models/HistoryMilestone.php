<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class HistoryMilestone extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'year',
        'event_type',
        'order',
        'status',
        // Note: removed 'image' – media library handles it now
    ];

    public $translatable = ['title', 'description'];

    protected $casts = [
        'year'  => 'integer',
        'order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('milestone_image')   // better name than generic 'images'
        ->singleFile()                             // most timelines use one key image per event
        ->useDisk('public');

        // If you want multiple images per milestone, use:
        // $this->addMediaCollection('gallery')
        //     ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600);
    }
}
