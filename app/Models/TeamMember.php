<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class TeamMember extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'education',        // translatable JSON [{degree, institution, year}]
        'experience',       // translatable JSON [{role, organization, duration}]
        'department_id',
        'email',
        'phone',
        'linkedin_url',
        'twitter_url',
        'facebook_url',
        'skills',           // non-translatable JSON [{name, level}]
        'order',
        'status',
    ];

    public $translatable = [
        'name',
        'position',
        'bio',
        'education',
        'experience',
    ];

    protected $casts = [
        'skills' => 'array',
        'order'  => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('image');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
