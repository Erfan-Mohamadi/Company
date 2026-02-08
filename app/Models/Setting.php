<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
/**
 * @property string|null $group
 * @property string|null $label
 * @property string $name
 * @property string $type
 * @property string|float|int|bool|null $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\MediaCollections\Models\Media[] $media
 * @method \Spatie\MediaLibrary\MediaCollections\Models\Media|null getFirstMedia(string $collectionName = 'default')
 * @method string getFirstMediaUrl(string $collectionName = 'default', string $conversion = '')
 */
class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'settings';

    protected $fillable = [
        'group', 'label', 'name', 'type', 'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────────────────────────────── Types ──────────────────────────────
    public const TYPE_TEXT     = 'text';
    public const TYPE_NUMBER   = 'number';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_IMAGE    = 'image';
    public const TYPE_VIDEO    = 'video';
    public const TYPE_TOGGLE   = 'toggle';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_TEXT     => __('Short Text'),
            self::TYPE_NUMBER   => __('Number'),
            self::TYPE_TEXTAREA => __('Long Text (Editor)'),
            self::TYPE_IMAGE    => __('Image File'),
            self::TYPE_VIDEO    => __('Video File'),
            self::TYPE_TOGGLE   => __('Toggle (On/Off)'),
        ];
    }

    // ───────────────────────────── Groups ──────────────────────────────
    public const GROUP_GENERAL = 'general';
    public const GROUP_SOCIAL  = 'social';
    public const GROUP_RULES   = 'rules';
    public const GROUP_ABOUT   = 'about';
    public const GROUP_FOOTER  = 'footer';
    public const GROUP_HOME    = 'home';
    public const GROUP_CONTACT = 'contact';

    public static function getAllGroups(): array
    {
        return [
            self::GROUP_GENERAL => [
                'title' => __('General Settings'),
                'summary' => __('General site settings like logo, phone, etc.'),
                'bg' => 'primary',
                'icon' => 'information-circle'
            ],
            self::GROUP_SOCIAL  => [
                'title' => __('Social Networks'),
                'summary' => __('Social networks like Instagram, etc.'),
                'bg' => 'pink',
                'icon' => 'share'
            ],
            self::GROUP_ABOUT   => [
                'title' => __('About Us Group'),
                'summary' => __('About us settings'),
                'bg' => 'success',
                'icon' => 'document-text'
            ],
            self::GROUP_CONTACT => [
                'title' => __('Contact Us Group'),
                'summary' => __('Contact us settings'),
                'bg' => 'warning',
                'icon' => 'phone'
            ],
            self::GROUP_HOME    => [
                'title' => __('Home Page Group'),
                'summary' => __('Home page settings'),
                'bg' => 'danger',
                'icon' => 'home'
            ],
            self::GROUP_FOOTER  => [
                'title' => __('Footer Group'),
                'summary' => __('Site footer settings'),
                'bg' => 'warning',
                'icon' => 'list-bullet'
            ],
            self::GROUP_RULES   => [
                'title' => __('Rules and Regulations Group'),
                'summary' => __('Site rules settings'),
                'bg' => 'primary',
                'icon' => 'check-circle'
            ],
        ];
    }

    // ───────────────────────────── Media Configuration ──────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('setting_files')
            ->singleFile() // Only one file per setting
            ->useDisk('public'); // Use public disk
        // REMOVED ->acceptsMimeTypes() - let Filament handle validation
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Optional: Add image conversions for thumbnails
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->performOnCollections('setting_files')
            ->nonQueued();
    }

    // ───────────────────────────── Mutators & Accessors ──────────────────────────────

    /**
     * Set the value attribute - handle number formatting to avoid scientific notation
     */
    public function setValueAttribute($value): void
    {
        // Handle null or empty values
        if ($value === null || $value === '') {
            $this->attributes['value'] = null;
            return;
        }

        // For number types, ensure we store as string to avoid scientific notation
        if ($this->type === self::TYPE_NUMBER) {
            // Convert to string and remove scientific notation
            if (is_numeric($value)) {
                // Use number_format to prevent scientific notation, remove commas
                $this->attributes['value'] = str_replace(',', '', number_format((float)$value, 10, '.', ''));
                // Remove trailing zeros after decimal point
                $this->attributes['value'] = rtrim(rtrim($this->attributes['value'], '0'), '.');
            } else {
                $this->attributes['value'] = $value;
            }
            return;
        }

        // For toggle types, ensure boolean values are stored as 0/1
        if ($this->type === self::TYPE_TOGGLE) {
            $this->attributes['value'] = $value ? '1' : '0';
            return;
        }

        // For all other types, store as-is
        $this->attributes['value'] = $value;
    }

    /**
     * Get the value attribute - return proper types
     */
    public function getValueAttribute($value)
    {
        // For image/video types, return the media URL instead of stored value
        if (in_array($this->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
            return $this->getFirstMediaUrl('setting_files') ?: $value;
        }

        // For number types, return as numeric value (not scientific notation)
        if ($this->type === self::TYPE_NUMBER && $value !== null) {
            return $value; // Already stored as string without scientific notation
        }

        // For toggle types, return as boolean
        if ($this->type === self::TYPE_TOGGLE) {
            return $value === '1' || $value === 1 || $value === true || $value === 'true';
        }

        return $value;
    }

    /**
     * Helper attribute for getting file info (backwards compatibility)
     */
    public function getFileAttribute(): ?array
    {
        $media = $this->getFirstMedia('setting_files');

        if (!$media) {
            return null;
        }

        return [
            'id'   => $media->id,
            'url'  => $media->getFullUrl(),
            'name' => $media->file_name,
            'size' => $media->size,
            'mime_type' => $media->mime_type,
        ];
    }

    // ───────────────────────────── Model Events ──────────────────────────────

    protected static function booted(): void
    {
        // Clean up media when setting is deleted
        static::deleting(function (Setting $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                $setting->clearMediaCollection('setting_files');
            }
        });

        // Sync media URL to value column after media is added
        static::saved(function (Setting $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                $media = $setting->getFirstMedia('setting_files');
                if ($media && $setting->value !== $media->getFullUrl()) {
                    // Update value without triggering saved event again
                    $setting->updateQuietly(['value' => $media->getFullUrl()]);
                }
            }
        });
    }
}
