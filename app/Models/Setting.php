<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Http\UploadedFile;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'settings';

    protected $fillable = [
        'group',
        'label',
        'name',
        'type',
        'value',
    ];

    protected $casts = [
        'value' => 'string',
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
            self::GROUP_GENERAL => [
                'title'   => 'تنظیمات عمومی',
                'summary' => 'تنظیمات عمومی سایت مانند لوگو و تلفن و ... در این بخش قرار می گیرد.',
                'bg'      => 'primary',
                'icon'    => 'information-circle',
            ],
            self::GROUP_SOCIAL => [
                'title'   => 'شبکه های اجتماعی',
                'summary' => 'شبکه های اجتماعی مانند اینستاگرام و ... در این بخش قرار می گیرد.',
                'bg'      => 'pink',
                'icon'    => 'share',
            ],
            self::GROUP_ABOUT => [
                'title'   => 'گروه درباره ما',
                'summary' => 'تنظیمات درباره ما در این بخش قرار می گیرد.',
                'bg'      => 'success',
                'icon'    => 'document-text',
            ],
            self::GROUP_CONTACT => [
                'title'   => 'گروه تماس با ما',
                'summary' => 'تنظیمات تماس با ما در این بخش قرار می گیرد.',
                'bg'      => 'warning',
                'icon'    => 'phone',
            ],
            self::GROUP_HOME => [
                'title'   => 'گروه صفحه اصلی',
                'summary' => 'تنظیمات صفحه اصلی در این بخش قرار می گیرد.',
                'bg'      => 'danger',
                'icon'    => 'home',
            ],
            self::GROUP_FOOTER => [
                'title'   => 'گروه فوتر',
                'summary' => 'تنظیمات فوتر سایت در این بخش قرار می گیرد.',
                'bg'      => 'warning',
                'icon'    => 'list-bullet',
            ],
            self::GROUP_RULES => [
                'title'   => 'گروه قوانین و مقررات',
                'summary' => 'تنظیمات قوانین سایت در این بخش قرار می گیرد.',
                'bg'      => 'primary',
                'icon'    => 'check-circle',
            ],
        ];
    }

    // ───────────────────────────── Media ──────────────────────────────
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('setting_files')
            ->singleFile()
            ->useDisk('public');
    }

    /**
     * For frontend: return media URL for image/video types
     */
    public function getValueAttribute($value)
    {
        if (in_array($this->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
            $media = $this->getFirstMedia('setting_files');
            return $media ? $media->getFullUrl() : null;
        }

        return $value;
    }

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
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Setting $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                $setting->clearMediaCollection('setting_files');
            }
        });
    }

    protected function casts(): array
    {
        $casts = ['value' => 'string'];

        if ($this->type === self::TYPE_TOGGLE) {
            $casts['value'] = 'boolean';
        }

        return $casts;
    }
}
