<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'is_active',
        'extras',
    ];
    
    protected $casts = [
        'extras' => 'array',
        'is_active' => 'boolean',
    ];
    
}
