<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TestMediaUpload extends Command
{
    protected $signature = 'test:media-upload';
    protected $description = 'Test if media library is working correctly';

    public function handle()
    {
        $this->info('Testing Spatie Media Library setup...');
        $this->newLine();

        // 1. Check if media table exists
        $this->info('1. Checking media table...');
        try {
            $mediaCount = DB::table('media')->count();
            $this->line("   ✓ Media table exists ({$mediaCount} records)");
        } catch (\Exception $e) {
            $this->error("   ✗ Media table not found!");
            $this->error("   Run: php artisan migrate");
            return Command::FAILURE;
        }

        // 2. Check storage directory
        $this->info('2. Checking storage directories...');
        $publicPath = storage_path('app/public');
        $settingsPath = storage_path('app/public/settings');

        if (is_dir($publicPath) && is_writable($publicPath)) {
            $this->line("   ✓ storage/app/public exists and is writable");
        } else {
            $this->error("   ✗ storage/app/public not writable!");
            $this->error("   Run: chmod -R 775 storage");
            return Command::FAILURE;
        }

        if (!is_dir($settingsPath)) {
            mkdir($settingsPath, 0775, true);
            $this->line("   ✓ Created storage/app/public/settings");
        } else {
            $this->line("   ✓ storage/app/public/settings exists");
        }

        // 3. Check storage link
        $this->info('3. Checking storage symlink...');
        $symlinkPath = public_path('storage');
        if (is_link($symlinkPath)) {
            $this->line("   ✓ public/storage symlink exists");
        } else {
            $this->error("   ✗ public/storage symlink missing!");
            $this->error("   Run: php artisan storage:link");
            return Command::FAILURE;
        }

        // 4. Check if Setting model implements HasMedia
        $this->info('4. Checking Setting model...');
        $setting = Setting::query()->where('type', 'image')->first();

        if (!$setting) {
            $this->warn("   ! No image type settings found");
            $this->line("   Create one in admin panel: /admin/settings");
        } else {
            $this->line("   ✓ Found image setting: {$setting->name}");

            // Check if it's a HasMedia model
            if (method_exists($setting, 'addMedia')) {
                $this->line("   ✓ Setting model implements HasMedia");
            } else {
                $this->error("   ✗ Setting model doesn't implement HasMedia!");
                return Command::FAILURE;
            }
        }

        // 5. Create a test image file
        $this->info('5. Creating test image...');
        $testImagePath = storage_path('app/test-upload.jpg');

        // Create a simple 1x1 pixel JPEG
        $imageData = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlbaWmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9/KKKKAP/2Q==');
        file_put_contents($testImagePath, $imageData);
        $this->line("   ✓ Test image created at: {$testImagePath}");

        // 6. Try to attach media to a setting
        if ($setting) {
            $this->info('6. Testing media upload...');

            try {
                // Add media
                $media = $setting->addMedia($testImagePath)
                    ->toMediaCollection('setting_files');

                $this->line("   ✓ Media attached successfully!");
                $this->line("   Media ID: {$media->id}");
                $this->line("   File name: {$media->file_name}");
                $this->line("   Collection: {$media->collection_name}");

                // Get URL
                $url = $setting->getFirstMediaUrl('setting_files');
                $this->line("   Media URL: {$url}");

                // Check if file exists in storage
                $filePath = $media->getPath();
                if (file_exists($filePath)) {
                    $this->line("   ✓ File exists in storage: {$filePath}");
                } else {
                    $this->error("   ✗ File NOT found in storage!");
                }

                // Update setting value
                $setting->update(['value' => $url]);
                $this->line("   ✓ Setting value updated: {$setting->value}");

                // Clean up test
                $this->newLine();
                if ($this->confirm('Delete test media?', true)) {
                    $media->delete();
                    $this->line("   ✓ Test media deleted");
                }

            } catch (\Exception $e) {
                $this->error("   ✗ Media upload failed!");
                $this->error("   Error: {$e->getMessage()}");
                return Command::FAILURE;
            }
        }

        // Clean up test image
        if (file_exists($testImagePath)) {
            unlink($testImagePath);
        }

        $this->newLine();
        $this->info('✅ All tests passed! Media library is working correctly.');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Make sure GroupSettings.php has ->model($setting) on media fields');
        $this->line('2. Try uploading an image in /admin/settings');

        return Command::SUCCESS;
    }
}
