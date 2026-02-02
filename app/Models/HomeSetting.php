<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'group', 'name', 'label', 'type', 'value'
    ];

    // Your existing constants for types & groups
    public const TYPE_TEXT     = 'text';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_NUMBER   = 'number';
    public const TYPE_IMAGE    = 'image';
    public const TYPE_VIDEO    = 'video';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_TEXT     => 'متن کوتاه',
            self::TYPE_TEXTAREA => 'متن بلند',
            self::TYPE_NUMBER   => 'عددی',
            self::TYPE_IMAGE    => 'تصویر',
            self::TYPE_VIDEO    => 'ویدئو',
        ];
    }

// ─── Setting Groups ────────────────────────────────────────────────
    public const GROUP_HERO          = 'hero';
    public const GROUP_ABOUT         = 'about';
    public const GROUP_INFO_BOXES    = 'info_boxes';
    public const GROUP_SERVICES      = 'services';
    public const GROUP_WHY_US        = 'why_us';
    public const GROUP_COMPREHENSIVE = 'comprehensive';
    public const GROUP_CONTACT       = 'contact';

    public static function getAllGroups(): array
    {
        return [
            self::GROUP_HERO => [
                'title'   => 'بخش هیرو',
                'summary' => 'تنظیمات بخش هیرو صفحه اصلی',
                'icon'    => 'home',
                'bg'      => 'primary',
            ],
            self::GROUP_ABOUT => [
                'title'   => 'بخش درباره ما',
                'summary' => 'تنظیمات بخش درباره ما',
                'icon'    => 'info',
                'bg'      => 'success',
            ],
            self::GROUP_INFO_BOXES => [
                'title'   => 'باکس‌های اطلاعاتی',
                'summary' => 'تنظیمات باکس‌های اطلاعاتی',
                'icon'    => 'grid',
                'bg'      => 'warning',
            ],
            self::GROUP_SERVICES => [
                'title'   => 'بخش خدمات',
                'summary' => 'تنظیمات بخش خدمات',
                'icon'    => 'briefcase',
                'bg'      => 'info',
            ],
            self::GROUP_WHY_US => [
                'title'   => 'چرا ما',
                'summary' => 'تنظیمات بخش چرا ما',
                'icon'    => 'star',
                'bg'      => 'danger',
            ],
            self::GROUP_COMPREHENSIVE => [
                'title'   => 'خدمات جامع',
                'summary' => 'تنظیمات خدمات جامع',
                'icon'    => 'package',
                'bg'      => 'secondary',
            ],
            self::GROUP_CONTACT => [
                'title'   => 'تماس با ما',
                'summary' => 'تنظیمات بخش تماس',
                'icon'    => 'phone',
                'bg'      => 'dark',
            ],
        ];
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('home_setting_files')->singleFile();
    }

    protected static function booted(): void
    {
        static::saved(function (self $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                if ($media = $setting->getFirstMedia('home_setting_files')) {
                    $setting->updateQuietly(['value' => $media->getFullUrl()]);
                }
            }
        });

        static::deleting(function (self $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                $setting->clearMediaCollection('home_setting_files');
            }
        });
    }
}
