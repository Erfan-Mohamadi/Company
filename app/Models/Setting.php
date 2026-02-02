<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Setting extends Model implements HasMedia
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

    public const TYPE_CHECKBOX = 'checkbox';

    public static function getAllTypes(): array
    {
        return [
            self::TYPE_TEXT     => __('Short Text'),
            self::TYPE_TEXTAREA => __('Long Text'),
            self::TYPE_NUMBER   => __('Number'),
            self::TYPE_IMAGE    => __('Image'),
            self::TYPE_VIDEO    => __('Video'),
            self::TYPE_CHECKBOX => __('Checkbox'),
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
                'title'   => __('Hero Section'),
                'summary' => __('Hero section settings'),
                'icon'    => 'home',
                'bg'      => 'primary',
            ],
            self::GROUP_ABOUT => [
                'title'   => __('About Us Section'),
                'summary' => __('About us section settings'),
                'icon'    => 'info',
                'bg'      => 'success',
            ],
            self::GROUP_INFO_BOXES => [
                'title'   => __('Info Boxes'),
                'summary' => __('Info boxes settings'),
                'icon'    => 'grid',
                'bg'      => 'warning',
            ],
            self::GROUP_SERVICES => [
                'title'   => __('Services Section'),
                'summary' => __('Services section settings'),
                'icon'    => 'briefcase',
                'bg'      => 'info',
            ],
            self::GROUP_WHY_US => [
                'title'   => __('Why Us Section'),
                'summary' => __('Why us section settings'),
                'icon'    => 'star',
                'bg'      => 'danger',
            ],
            self::GROUP_COMPREHENSIVE => [
                'title'   => __('Comprehensive Services'),
                'summary' => __('Comprehensive services settings'),
                'icon'    => 'package',
                'bg'      => 'secondary',
            ],
            self::GROUP_CONTACT => [
                'title'   => __('Contact Section'),
                'summary' => __('Contact section settings'),
                'icon'    => 'phone',
                'bg'      => 'dark',
            ],
        ];
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('setting_files')->singleFile();
    }

    protected static function booted(): void
    {
        static::saved(function (self $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                if ($media = $setting->getFirstMedia('setting_files')) {
                    $setting->updateQuietly(['value' => $media->getFullUrl()]);
                }
            }
        });

        static::deleting(function (self $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                $setting->clearMediaCollection('setting_files');
            }
        });
    }
}
