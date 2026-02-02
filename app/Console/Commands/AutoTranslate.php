<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Stichoza\GoogleTranslate\GoogleTranslate;

class AutoTranslate extends Command
{
    protected $signature = 'translations:auto
                            {--from=en : Source language code}
                            {--to=fa : Target language code}
                            {--force : Overwrite existing translations}
                            {--limit= : Limit number of translations (for testing)}
                            {--delay=1 : Delay between requests in seconds}
                            {--dry-run : Show what would be translated without saving}';

    protected $description = 'Auto-translate missing translation keys';

    protected int $successCount = 0;
    protected int $failedCount = 0;
    protected int $skippedCount = 0;
    protected array $failedKeys = [];

    public function handle(): int
    {
        $from = $this->option('from');
        $to = $this->option('to');
        $force = $this->option('force');
        $limit = $this->option('limit');
        $delay = (int) $this->option('delay');
        $dryRun = $this->option('dry-run');

        // Validate inputs
        if ($delay < 1) {
            $this->error('❌ Delay must be at least 1 second to avoid rate limiting');
            return Command::FAILURE;
        }

        $sourcePath = lang_path("{$from}.json");
        $targetPath = lang_path("{$to}.json");

        // Check if source file exists
        if (!File::exists($sourcePath)) {
            $this->error("❌ Source file not found: {$sourcePath}");
            $this->info("💡 Run 'php artisan translations:generate' first");
            return Command::FAILURE;
        }

        // Load files
        $source = json_decode(File::get($sourcePath), true) ?? [];
        $target = File::exists($targetPath)
            ? json_decode(File::get($targetPath), true) ?? []
            : [];

        if (empty($source)) {
            $this->warn('⚠ No translation keys found in source file');
            return Command::FAILURE;
        }

        // Filter keys that need translation
        $keysToTranslate = [];
        foreach ($source as $key => $value) {
            if (!isset($target[$key]) || $force) {
                $keysToTranslate[$key] = $value;
            } else {
                $this->skippedCount++;
            }
        }

        if (empty($keysToTranslate)) {
            $this->info('✅ All keys are already translated!');
            $this->info("   Total keys: " . count($source));
            return Command::SUCCESS;
        }

        // Apply limit if specified
        if ($limit) {
            $keysToTranslate = array_slice($keysToTranslate, 0, $limit, true);
            $this->warn("⚠ Limited to {$limit} translations for testing");
        }

        // Show summary
        $this->info('📊 Translation Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total keys', count($source)],
                ['Already translated', $this->skippedCount],
                ['To translate', count($keysToTranslate)],
                ['Language', "{$from} → {$to}"],
                ['Delay', "{$delay} second(s)"],
            ]
        );
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN - Showing first 5 keys that would be translated:');
            $sample = array_slice($keysToTranslate, 0, 5, true);
            foreach ($sample as $key => $value) {
                $this->line("  {$key} → [would translate]");
            }
            return Command::SUCCESS;
        }

        if (!$this->confirm('Continue with auto-translation?', true)) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        // Initialize translator
        try {
            $translator = new GoogleTranslate();
            $translator->setSource($from);
            $translator->setTarget($to);
        } catch (\Exception $e) {
            $this->error('❌ Failed to initialize translator: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Translate with progress bar
        $this->info('🤖 Translating...');
        $bar = $this->output->createProgressBar(count($keysToTranslate));
        $bar->start();

        foreach ($keysToTranslate as $key => $value) {
            try {
                // Translate
                $translated = $translator->translate($value);

                if (empty($translated)) {
                    throw new \Exception('Empty translation returned');
                }

                $target[$key] = $translated;
                $this->successCount++;

                // Rate limiting delay
                sleep($delay);

            } catch (\Exception $e) {
                $this->failedCount++;
                $this->failedKeys[$key] = $e->getMessage();

                // Keep original value if translation fails
                $target[$key] = $value;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Sort and save
        ksort($target);
        File::put(
            $targetPath,
            json_encode($target, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        // Show results
        $this->displayResults($from, $to);

        return $this->failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function displayResults(string $from, string $to): void
    {
        $this->info('📊 Translation Results:');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Successfully translated', $this->successCount],
                ['⏭ Skipped (already exists)', $this->skippedCount],
                ['❌ Failed', $this->failedCount],
            ]
        );

        if ($this->failedCount > 0) {
            $this->newLine();
            $this->warn('⚠ Failed translations (kept original text):');
            foreach (array_slice($this->failedKeys, 0, 10, true) as $key => $error) {
                $this->line("  - {$key}");
                $this->line("    Error: {$error}");
            }
            if (count($this->failedKeys) > 10) {
                $this->line("  ... and " . (count($this->failedKeys) - 10) . " more");
            }
        }

        $this->newLine();

        if ($this->successCount > 0) {
            $this->info("✅ Translation complete! Updated: lang/{$to}.json");
            $this->comment('💡 Please review the translations for accuracy');
        }
    }
}
