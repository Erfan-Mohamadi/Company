<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class GoalStrategy extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'short_summary',
        'type',
        'order',
        'icon',
        'status',
    ];

    public $translatable = [
        'title',
        'description',
        'short_summary',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
