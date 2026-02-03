<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'group', 'name', 'label', 'type', 'value'
    ];

    // Don't cast value automatically - we handle it in the accessor
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    public const GROUP_HOME          = 'home';
    public const GROUP_SERVICES      = 'services';
    public const GROUP_SOCIAL        = 'social';
    public const GROUP_GENERAL       = 'general';
    public const GROUP_CONTACT       = 'contact';

    public static function getAllGroups(): array
    {
        return [
            self::GROUP_GENERAL => [
                'title'   => __('General section'),
                'summary' => __('General section settings'),
                'icon'    => 'package',
                'bg'      => 'secondary',
            ],
            self::GROUP_HERO => [
                'title'   => __('Hero Section'),
                'summary' => __('Hero section settings'),
                'icon'    => 'home',
                'bg'      => 'primary',
            ],
            self::GROUP_SOCIAL => [
                'title'   => __('Social Section'),
                'summary' => __('Social section settings'),
                'icon'    => 'star',
                'bg'      => 'danger',
            ],
            self::GROUP_ABOUT => [
                'title'   => __('About Us Section'),
                'summary' => __('About us section settings'),
                'icon'    => 'info',
                'bg'      => 'success',
            ],
            self::GROUP_HOME => [
                'title'   => __('Home page'),
                'summary' => __('Home page settings'),
                'icon'    => 'grid',
                'bg'      => 'warning',
            ],
            self::GROUP_SERVICES => [
                'title'   => __('Services Section'),
                'summary' => __('Services section settings'),
                'icon'    => 'briefcase',
                'bg'      => 'info',
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
        // existing single-file collection for the 'file' uploader
        $this->addMediaCollection('setting_files')->singleFile();

        // new collection for rich-editor attachments (multiple files)
        $this->addMediaCollection('rich_editor');
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // You can add conversions here if needed
        // $this->addMediaConversion('thumb')->width(200)->height(200);
    }

    protected static function booted(): void
    {
        // Handle media deletion when setting is deleted
        static::deleting(function (self $setting) {
            if (in_array($setting->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
                $setting->clearMediaCollection('setting_files');
            }
        });

        // After saving, convert any temporary editor uploads into Spatie media.
        // This enables the "create" flow where the editor uploads before the DB record exists.
        static::saved(function (self $setting) {
            // Only for textarea types where images may be embedded in HTML
            if (($setting->type ?? null) !== self::TYPE_TEXTAREA) {
                return;
            }

            // Get the stored HTML value (raw)
            $html = $setting->getRawOriginal('value') ?? $setting->value ?? '';

            // Find image src attributes
            if (! preg_match_all('/<img[^>]+src=[\'"](?P<src>[^\'"]+)[\'"][^>]*>/i', $html, $matches)) {
                return;
            }

            $srcs = $matches['src'] ?? [];
            if (empty($srcs)) {
                return;
            }

            $tempPrefix = Storage::disk('public')->url('editor/temp'); // e.g. /storage/editor/temp
            $updatedHtml = $html;
            $moved = false;

            foreach ($srcs as $src) {
                // Only handle images uploaded to the temporary editor folder
                if (strpos($src, $tempPrefix) === false) {
                    continue;
                }

                // Derive filename from src URL path
                $path = parse_url($src, PHP_URL_PATH); // /storage/editor/temp/filename.jpg
                $filename = basename($path);
                $localPath = storage_path('app/public/editor/temp/' . $filename);

                if (! file_exists($localPath)) {
                    continue;
                }

                try {
                    // Add the file to Spatie media (uses the existing model)
                    $media = $setting->addMedia($localPath)->preservingOriginal()->toMediaCollection('rich_editor');

                    // Replace the src with the managed media URL
                    $newUrl = $media->getFullUrl();
                    $updatedHtml = str_replace($src, $newUrl, $updatedHtml);

                    // Optionally remove the temporary file
                    @unlink($localPath);

                    $moved = true;
                } catch (\Throwable $e) {
                    // If something goes wrong, skip this file but continue for others.
                    // You can log here if desired.
                    \Log::error('Failed moving temp editor file to media library: ' . $e->getMessage());
                    continue;
                }
            }

            if ($moved && $updatedHtml !== $html) {
                // Save quietly (avoid triggering observers again)
                $setting->value = $updatedHtml;
                $setting->saveQuietly();
            }
        });
    }

    /**
     * Get the value attribute for image/video types
     * This will return the media URL if media exists, otherwise return stored value
     */
    public function getValueAttribute($value)
    {
        // For image and video types, try to get media URL
        if (in_array($this->type, [self::TYPE_IMAGE, self::TYPE_VIDEO])) {
            $media = $this->getFirstMedia('setting_files');
            if ($media) {
                return $media->getFullUrl();
            }
        }

        // Return raw value for all other types (including textarea and checkbox)
        return $value;
    }

}
