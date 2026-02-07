<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
            self::TYPE_TEXT     => 'متن کوتاه',
            self::TYPE_NUMBER   => 'عددی',
            self::TYPE_TEXTAREA => 'متن بلند (ویرایشگر)',
            self::TYPE_IMAGE    => 'فایل عکس',
            self::TYPE_VIDEO    => 'فایل ویدئو',
            self::TYPE_TOGGLE   => 'تغییر وضعیت (روشن/خاموش)',
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
            self::GROUP_GENERAL => ['title' => 'تنظیمات عمومی',   'summary' => 'تنظیمات عمومی سایت مانند لوگو و تلفن و ...', 'bg' => 'primary', 'icon' => 'information-circle'],
            self::GROUP_SOCIAL  => ['title' => 'شبکه های اجتماعی', 'summary' => 'شبکه های اجتماعی مانند اینستاگرام و ...', 'bg' => 'pink', 'icon' => 'share'],
            self::GROUP_ABOUT   => ['title' => 'گروه درباره ما',   'summary' => 'تنظیمات درباره ما', 'bg' => 'success', 'icon' => 'document-text'],
            self::GROUP_CONTACT => ['title' => 'گروه تماس با ما',  'summary' => 'تنظیمات تماس با ما', 'bg' => 'warning', 'icon' => 'phone'],
            self::GROUP_HOME    => ['title' => 'گروه صفحه اصلی',  'summary' => 'تنظیمات صفحه اصلی', 'bg' => 'danger', 'icon' => 'home'],
            self::GROUP_FOOTER  => ['title' => 'گروه فوتر',       'summary' => 'تنظیمات فوتر سایت', 'bg' => 'warning', 'icon' => 'list-bullet'],
            self::GROUP_RULES   => ['title' => 'گروه قوانین و مقررات', 'summary' => 'تنظیمات قوانین سایت', 'bg' => 'primary', 'icon' => 'check-circle'],
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

    // ───────────────────────────── Accessors ──────────────────────────────

    /**
     * For image/video types, return the media URL instead of stored value
     * This makes it easy to use in frontend: {{ $setting->value }}
     */
    public function getValueAttribute($value)
    {
        if (in_array($this->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
            return $this->getFirstMediaUrl('setting_files') ?: $value;
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
