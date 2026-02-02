<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConvertPersianKeys extends Command
{
    protected $signature = 'translations:convert-keys
                            {--dry-run : Show changes without modifying files}';

    protected $description = 'Convert Persian text in code to use English translation keys';

    protected array $replacements = [
        "'تیم ما'" => "__('Our Team')",
        "'داستان رشد'" => "__('Growth Story')",
        "'درباره ما'" => "__('About Us')",
        "'دستاورد‌ها'" => "__('Achievements')",
        "'شبکه کسب‌وکار'" => "__('Business Network')",
        "'فعال'" => "__('Active')",
        "'قیمت'" => "__('Price')",
        "'مثال: آبی'" => "__('Example: Blue')",
        "'مثال: رنگ'" => "__('Example: Color')",
        "'مقدار'" => "__('Value')",
        "'نام محصول'" => "__('Product Name')",
        "'وضعیت'" => "__('Status')",
        "'وضعیت فعال بودن'" => "__('Active Status')",
        "'ویژگی‌ها'" => "__('Features')",
        "'پروفایل شرکت'" => "__('Company Profile')",
        "'کلید'" => "__('Key')",
    ];

    public function handle(): int
    {
        $this->info('🔄 Converting Persian keys to English in code files...');
        $this->newLine();

        $directories = [
            app_path('Filament'),
            app_path('Providers/Filament'),
        ];

        $modifiedFiles = 0;
        $totalReplacements = 0;

        foreach ($directories as $directory) {
            if (!File::isDirectory($directory)) {
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $content = File::get($file->getPathname());
                $originalContent = $content;
                $fileReplacements = 0;

                foreach ($this->replacements as $persian => $english) {
                    $count = 0;
                    $content = str_replace($persian, $english, $content, $count);
                    $fileReplacements += $count;
                }

                if ($fileReplacements > 0) {
                    $relativePath = str_replace(base_path() . '/', '', $file->getPathname());
                    $this->info("✓ {$relativePath} ({$fileReplacements} replacements)");

                    if (!$this->option('dry-run')) {
                        File::put($file->getPathname(), $content);
                    }

                    $modifiedFiles++;
                    $totalReplacements += $fileReplacements;
                }
            }
        }

        $this->newLine();

        if ($totalReplacements === 0) {
            $this->info('✓ No Persian keys found in code');
        } else {
            $this->info("✅ Modified {$modifiedFiles} files with {$totalReplacements} replacements");

            if ($this->option('dry-run')) {
                $this->warn('🔍 DRY RUN - No files were modified');
            }
        }

        return Command::SUCCESS;
    }
}
