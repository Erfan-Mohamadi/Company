<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SyncSettingMediaValues extends Command
{
    protected $signature = 'settings:sync-media';
    protected $description = 'Sync media URLs to value column for image and video settings';

    public function handle()
    {
        $this->info('Syncing setting media values...');

        $settings = Setting::whereIn('type', [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])->get();

        $synced = 0;
        foreach ($settings as $setting) {
            $media = $setting->getFirstMedia('setting_files');
            if ($media) {
                $setting->update(['value' => $media->getFullUrl()]);
                $this->line("✓ Synced {$setting->name}: {$media->getFullUrl()}");
                $synced++;
            } else {
                $this->warn("✗ No media found for {$setting->name}");
            }
        }

        $this->info("Synced {$synced} settings.");
        return Command::SUCCESS;
    }
}
