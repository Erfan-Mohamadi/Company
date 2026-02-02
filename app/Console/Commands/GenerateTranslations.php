<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateTranslations extends Command
{
    protected $signature = 'translations:generate
                        {--dry-run : Show what would be generated without saving}
                        {--detailed : Show detailed output}';

    protected $description = 'Scan Filament files and extract translation keys';

    protected array $patterns = [
        "/__\('([^']+)'\)/",                    // __('text')
        "/__\(\"([^\"]+)\"\)/",                 // __("text")
        "/->label\('([^']+)'\)/",               // ->label('text')
        "/->label\(\"([^\"]+)\"\)/",            // ->label("text")
        "/->description\('([^']+)'\)/",         // ->description('text')
        "/->description\(\"([^\"]+)\"\)/",      // ->description("text")
        "/->helperText\('([^']+)'\)/",          // ->helperText('text')
        "/->placeholder\('([^']+)'\)/",         // ->placeholder('text')
        "/->modalHeading\('([^']+)'\)/",        // ->modalHeading('text')
        "/->modalDescription\('([^']+)'\)/",    // ->modalDescription('text')
        "/->modalSubmitActionLabel\('([^']+)'\)/", // ->modalSubmitActionLabel('text')
        "/->modalCancelActionLabel\('([^']+)'\)/", // ->modalCancelActionLabel('text')
    ];

    protected array $excludePatterns = [
        '/\$/',      // Contains variables like $record
        '/\{/',      // Contains placeholders like {name}
        '/\\\/',     // Contains escape characters
        '/^\s*$/',   // Empty strings
    ];

    public function handle(): int
    {
        $this->info('🔍 Scanning Filament files for translation keys...');
        $this->newLine();

        $translations = [];
        $fileCount = 0;

        // Directories to scan
        $directories = [
            app_path('Filament'),
            app_path('Providers/Filament'),
        ];

        // Scan each directory
        foreach ($directories as $directory) {
            if (!File::isDirectory($directory)) {
                $this->warn("⚠ Directory not found: {$directory}");
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $fileCount++;
                $content = File::get($file->getPathname());
                $relativePath = str_replace(base_path() . '/', '', $file->getPathname());

                if ($this->option('detailed')) {
                    $this->line("  Scanning: {$relativePath}");
                }

                // Extract keys from this file
                $keysFound = $this->extractKeysFromContent($content);

                foreach ($keysFound as $key) {
                    if (!$this->shouldExcludeKey($key)) {
                        $translations[$key] = $key;
                    }
                }
            }
        }

        if (empty($translations)) {
            $this->warn('❌ No translation keys found!');
            return Command::FAILURE;
        }

        $this->info("✓ Scanned {$fileCount} files");
        $this->info("✓ Found " . count($translations) . " unique translation keys");
        $this->newLine();

        // Show sample keys
        if ($this->option('detailed')) {
            $this->info('Sample keys found:');
            $sample = array_slice(array_keys($translations), 0, 10);
            foreach ($sample as $key) {
                $this->line("  - {$key}");
            }
            if (count($translations) > 10) {
                $this->line("  ... and " . (count($translations) - 10) . " more");
            }
            $this->newLine();
        }

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN - No files were modified');
            return Command::SUCCESS;
        }

        // Update translation files
        $this->updateTranslationFile('en', $translations);
        $this->updateTranslationFile('fa', $translations);

        $this->info('✅ Successfully updated translation files:');
        $this->line('  → lang/en.json');
        $this->line('  → lang/fa.json');
        $this->newLine();
        $this->comment('💡 Next step: Run "php artisan translations:auto" to auto-translate');

        return Command::SUCCESS;
    }

    protected function extractKeysFromContent(string $content): array
    {
        $keys = [];

        foreach ($this->patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);

            if (!empty($matches[1])) {
                $keys = array_merge($keys, $matches[1]);
            }
        }

        return array_unique($keys);
    }

    protected function shouldExcludeKey(string $key): bool
    {
        foreach ($this->excludePatterns as $pattern) {
            if (preg_match($pattern, $key)) {
                return true;
            }
        }

        return false;
    }

    protected function updateTranslationFile(string $locale, array $newTranslations): void
    {
        $path = lang_path("{$locale}.json");

        // Create lang directory if it doesn't exist
        if (!File::isDirectory(lang_path())) {
            File::makeDirectory(lang_path(), 0755, true);
        }

        // Load existing translations
        $existing = File::exists($path)
            ? json_decode(File::get($path), true) ?? []
            : [];

        // Merge: Keep existing translations, add new keys
        $merged = array_merge($newTranslations, $existing);

        // Sort alphabetically for easier management
        ksort($merged);

        // Save with pretty print and unicode support
        File::put(
            $path,
            json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
