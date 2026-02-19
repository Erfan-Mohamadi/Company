<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Accreditation extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'organization_name',
        'description',
        'accreditation_type',
        'membership_number',
        'member_since',
        'start_date',
        'end_date',
        'verification_url',
        'order',
        'status',
    ];

    public $translatable = [
        'organization_name',
        'description',
        'accreditation_type',
    ];

    protected $casts = [
        'member_since' => 'date',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'order'        => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('certificate')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->performOnCollections('logo');
    }
}
