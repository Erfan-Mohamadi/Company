<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Statistic extends Model
{
    use HasTranslations;

    protected $table = 'statistics';

    protected $fillable = [
        'title',
        'suffix',
        'prefix',
        'number',
        'icon',
        'animation_enabled',
        'order',
        'status',
    ];

    public $translatable = [
        'title',
        'suffix',
        'prefix',
    ];

    protected $casts = [
        'animation_enabled' => 'boolean',
        'order'             => 'integer',
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

    // ────────────────────────────── Helpers ──────────────────────────────

    /**
     * Returns the fully formatted display value in current locale.
     * e.g. prefix="+", number="500", suffix="K" → "+500K"
     */
    public function getFormattedNumberAttribute(): string
    {
        return trim(($this->prefix ?? '') . $this->number . ($this->suffix ?? ''));
    }
}
