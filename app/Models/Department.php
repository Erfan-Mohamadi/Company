<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Department extends Model implements HasMedia
{
    use InteractsWithMedia, HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'location',
        'employee_count',
        'head_name',
        'head_email',
        'head_phone',
        'order',
        'status',
    ];

    public $translatable = [
        'name',
        'description',
    ];

    protected $casts = [
        'order'          => 'integer',
        'employee_count' => 'integer',
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
            ->width(800)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('image');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function leadershipMembers(): HasMany
    {
        return $this->hasMany(LeadershipTeam::class);
    }
}
