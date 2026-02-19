<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ExportMarket extends Model
{
    use HasTranslations;

    protected $fillable = [
        'country_name', 'country_code', 'continent', 'region',
        'export_volume', 'export_value', 'main_products',
        'distributors_count', 'start_year', 'growth_rate',
        'map_coordinates', 'order', 'status',
    ];

    public $translatable = ['country_name', 'region'];

    protected $casts = [
        'main_products'      => 'array',
        'map_coordinates'    => 'array',
        'export_volume'      => 'integer',
        'distributors_count' => 'integer',
        'start_year'         => 'integer',
        'order'              => 'integer',
    ];
}
