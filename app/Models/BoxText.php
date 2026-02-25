<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class BoxText extends Model
{
    use HasTranslations;

    protected $table = 'box_texts';

    protected $fillable = [
        'header',
        'description',
        'order',
        'status',
    ];

    public $translatable = [
        'header',
        'description',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_DRAFT    => __('Draft'),
        ];
    }

    // ────────────────────────────── Scopes ──────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order');
    }
}
