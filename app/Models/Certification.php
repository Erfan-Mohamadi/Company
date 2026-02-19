<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Certification extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'certification_body',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'verification_url',
        'featured',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'description',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
        'featured'    => 'boolean',
        'order'       => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificate_image')
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
            ->performOnCollections('certificate_image');
    }
}
