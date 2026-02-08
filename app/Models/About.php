<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class About extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'header', 'description', 'founded_year', 'founder_name',
        'mission', 'vision', 'core_values', 'employees_count',
        'locations_count', 'clients_count', 'founder_message', 'status'
    ];

    protected $casts = [
        'core_values' => 'array',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->multiple();
        $this->addMediaCollection('founder_image')->singleFile();
        $this->addMediaCollection('video')->singleFile();
    }
}
