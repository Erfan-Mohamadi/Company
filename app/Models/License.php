<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class License extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'license_name',
        'description',
        'license_number',
        'license_type',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'order',
        'status',
    ];

    public $translatable = [
        'license_name',
        'description',
        'issuing_authority',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
        'order'       => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('license_file')
            ->singleFile()
            ->useDisk('public');
    }
}
