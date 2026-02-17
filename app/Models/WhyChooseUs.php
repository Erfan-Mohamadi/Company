<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class WhyChooseUs extends Model
{
    use HasTranslations;

    protected $table = 'why_choose_us';

    protected $fillable = [
        'title',
        'short_description',
        'items',
        'icon',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'short_description',
        'items',           // JSON array → each item can have translated title/desc
    ];

    protected $casts = [
        'items' => 'array',
        'order' => 'integer',
    ];
}
