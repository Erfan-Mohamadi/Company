<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class RepresentationLetter extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'header',
        'description',
        'company_name',
        'representative_name',
        'territory',
        'issue_date',
        'expiry_date',
        'order',
        'status',
    ];

    public $translatable = [
        'header',
        'description',
        'company_name',
        'representative_name',
        'territory',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
        'order'       => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('document_file')
            ->singleFile()
            ->useDisk('public');
    }
}
