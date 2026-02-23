<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Dealer extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'dealer_name', 'dealer_code', 'website_url',
        'contact_person', 'email', 'phone',
        'address', 'city', 'province', 'country', 'postal_code',
        'territory', 'contract_start_date', 'contract_end_date',
        'rating', 'order', 'status','partnership_type',
    ];

    public $translatable = ['dealer_name', 'territory'];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date'   => 'date',
        'order'               => 'integer',
        'rating'              => 'integer',
    ];

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
