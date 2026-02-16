<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CoreValue extends Model
{
    use HasTranslations;

    protected $fillable = [
        'value_name',
        'description',
        'icon',
        'order',
        'status',
    ];

    public $translatable = ['value_name', 'description'];

    protected $casts = [
        'order' => 'integer',
    ];
}
