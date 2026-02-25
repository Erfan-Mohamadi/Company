<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Popup extends Model
{
    use HasTranslations;

    protected $table = 'popups';

    protected $fillable = [
        'title',
        'content',
        'popup_type',
        'trigger_type',
        'display_delay',
        'frequency',
        'pages',
        'start_date',
        'end_date',
        'status',
    ];

    public $translatable = [
        'title',
        'content',
    ];

    protected $casts = [
        'pages'         => 'array',
        'display_delay' => 'integer',
        'start_date'    => 'datetime',
        'end_date'      => 'datetime',
    ];

    // ────────────────────────────── Constants ──────────────────────────────

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DRAFT    = 'draft';

    public const TYPE_ANNOUNCEMENT  = 'announcement';
    public const TYPE_NEWSLETTER    = 'newsletter';
    public const TYPE_PROMOTION     = 'promotion';
    public const TYPE_COOKIE_NOTICE = 'cookie_notice';
    public const TYPE_AGE_GATE      = 'age_gate';

    public const TRIGGER_ON_LOAD     = 'on_load';
    public const TRIGGER_ON_SCROLL   = 'on_scroll';
    public const TRIGGER_EXIT_INTENT = 'on_exit_intent';
    public const TRIGGER_ON_CLICK    = 'on_click';
    public const TRIGGER_TIMED       = 'timed';

    public const FREQUENCY_ONCE_SESSION = 'once_per_session';
    public const FREQUENCY_ONCE_DAY     = 'once_per_day';
    public const FREQUENCY_ONCE_WEEK    = 'once_per_week';
    public const FREQUENCY_ALWAYS       = 'always';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE   => __('Active'),
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_DRAFT    => __('Draft'),
        ];
    }

    public static function getPopupTypes(): array
    {
        return [
            self::TYPE_ANNOUNCEMENT  => __('Announcement'),
            self::TYPE_NEWSLETTER    => __('Newsletter'),
            self::TYPE_PROMOTION     => __('Promotion'),
            self::TYPE_COOKIE_NOTICE => __('Cookie Notice'),
            self::TYPE_AGE_GATE      => __('Age Gate'),
        ];
    }

    public static function getTriggerTypes(): array
    {
        return [
            self::TRIGGER_ON_LOAD     => __('On Page Load'),
            self::TRIGGER_ON_SCROLL   => __('On Scroll'),
            self::TRIGGER_EXIT_INTENT => __('Exit Intent'),
            self::TRIGGER_ON_CLICK    => __('On Click'),
            self::TRIGGER_TIMED       => __('Timed'),
        ];
    }

    public static function getFrequencies(): array
    {
        return [
            self::FREQUENCY_ONCE_SESSION => __('Once per Session'),
            self::FREQUENCY_ONCE_DAY     => __('Once per Day'),
            self::FREQUENCY_ONCE_WEEK    => __('Once per Week'),
            self::FREQUENCY_ALWAYS       => __('Always'),
        ];
    }

    // ────────────────────────────── Scopes ──────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(fn ($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()));
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('popup_type', $type);
    }

    // ────────────────────────────── Helpers ──────────────────────────────

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        $now = now();

        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }

        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if popup should show on the given URL path.
     * Null/empty pages array means show on all pages.
     */
    public function shouldShowOnPage(string $path): bool
    {
        if (empty($this->pages)) {
            return true;
        }

        foreach ($this->pages as $pattern) {
            if (str_contains($path, $pattern) || fnmatch($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
