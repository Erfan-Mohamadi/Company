<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixTranslations extends Command
{
    protected $signature = 'translations:fix';
    protected $description = 'Fix mixed Persian/English in translation files';

    // Map Persian text to proper English keys
    protected array $persianToEnglish = [
        'تیم ما' => 'Our Team',
        'داستان رشد' => 'Growth Story',
        'درباره ما' => 'About Us',
        'دستاورد‌ها' => 'Achievements',
        'شبکه کسب‌وکار' => 'Business Network',
        'فعال' => 'Active',
        'قیمت' => 'Price',
        'مثال: آبی' => 'Example: Blue',
        'مثال: رنگ' => 'Example: Color',
        'مقدار' => 'Value',
        'نام محصول' => 'Product Name',
        'وضعیت' => 'Status',
        'وضعیت فعال بودن' => 'Active Status',
        'ویژگی‌ها' => 'Features',
        'پروفایل شرکت' => 'Company Profile',
        'کلید' => 'Key',
    ];

    public function handle(): int
    {
        $this->info('🔧 Fixing translation files...');
        $this->newLine();

        // Load files
        $enPath = lang_path('en.json');
        $faPath = lang_path('fa.json');

        if (!File::exists($enPath) || !File::exists($faPath)) {
            $this->error('Translation files not found!');
            return Command::FAILURE;
        }

        $en = json_decode(File::get($enPath), true) ?? [];
        $fa = json_decode(File::get($faPath), true) ?? [];

        // Fix English file - remove Persian keys and fix encoding
        $enFixed = [];
        foreach ($en as $key => $value) {
            // Skip if key contains Persian characters
            if ($this->containsPersian($key)) {
                $this->warn("Removing Persian key from en.json: {$key}");
                continue;
            }

            // Skip filament-shield internal keys (keep them as-is)
            if (str_starts_with($key, 'filament-shield::')) {
                continue;
            }

            $enFixed[$key] = $value;
        }

        // Fix Persian file - convert Persian keys to English
        $faFixed = [];
        foreach ($fa as $key => $value) {
            // If key is in Persian, convert to English
            if ($this->containsPersian($key)) {
                if (isset($this->persianToEnglish[$key])) {
                    $englishKey = $this->persianToEnglish[$key];
                    $faFixed[$englishKey] = $value; // Keep Persian value
                    $enFixed[$englishKey] = $englishKey; // Add English key to en.json
                    $this->info("Converted: {$key} → {$englishKey}");
                } else {
                    $this->warn("Unknown Persian key (skipped): {$key}");
                }
            } else {
                // Keep existing proper translations
                $faFixed[$key] = $value;
            }
        }

        // Sort both
        ksort($enFixed);
        ksort($faFixed);

        // Backup originals
        File::copy($enPath, lang_path('en.json.backup'));
        File::copy($faPath, lang_path('fa.json.backup'));
        $this->info('✓ Created backups: en.json.backup, fa.json.backup');

        // Save fixed files
        File::put($enPath, json_encode($enFixed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put($faPath, json_encode($faFixed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->newLine();
        $this->info('✅ Fixed translation files:');
        $this->table(
            ['File', 'Before', 'After'],
            [
                ['en.json', count($en), count($enFixed)],
                ['fa.json', count($fa), count($faFixed)],
            ]
        );

        $this->newLine();
        $this->comment('💡 Check the files and restore from backup if needed');

        return Command::SUCCESS;
    }

    protected function containsPersian(string $text): bool
    {
        // Check if string contains Persian/Arabic characters
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1;
    }
}
