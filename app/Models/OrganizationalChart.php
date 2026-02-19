<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class OrganizationalChart extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'description',
        'hierarchy_data',
        'status',
    ];

    public $translatable = [
        'description',
    ];

    protected $casts = [
        'hierarchy_data' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('diagram')
            ->singleFile()
            ->useDisk('public');
    }
}
