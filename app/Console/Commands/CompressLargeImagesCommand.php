<?php

namespace App\Console\Commands;

use App\Services\ImageUploadOptimizer;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CompressLargeImagesCommand extends Command
{
    protected $signature = 'media:compress
                            {--dry-run : List files over the limit without changing them}
                            {--path= : Only scan under this path relative to storage/app/public}';

    protected $description = 'Compress existing images over 700 KB in storage/app/public';

    public function handle(ImageUploadOptimizer $optimizer): int
    {
        if (! extension_loaded('gd')) {
            $this->error('PHP GD extension is required for image compression.');

            return self::FAILURE;
        }

        $base = storage_path('app/public');
        $sub = $this->option('path');
        $root = $sub ? $base.'/'.trim($sub, '/') : $base;

        if (! is_dir($root)) {
            $this->error('Directory not found: '.$root);

            return self::FAILURE;
        }

        $maxBytes = $optimizer->maxBytes();
        $dryRun = (bool) $this->option('dry-run');
        $compressed = 0;
        $skipped = 0;
        $failed = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || ! preg_match('/\.(jpe?g|png|gif|webp)$/i', $file->getFilename())) {
                continue;
            }

            $path = $file->getPathname();
            clearstatcache(true, $path);
            $size = filesize($path);

            if ($size <= $maxBytes) {
                $skipped++;

                continue;
            }

            $relative = ltrim(str_replace($base, '', dirname($path)), '/');
            $folder = basename(dirname($path));
            $preset = ImageUploadOptimizer::presetForFolder($folder);

            $humanSize = round($size / 1024).' KB';
            $label = trim(str_replace($base, '', $path), '/');

            if ($dryRun) {
                $this->line("[dry-run] {$label} ({$humanSize}) → preset: {$preset}");
                $compressed++;

                continue;
            }

            if ($optimizer->compressPathIfNeeded($path, $preset)) {
                clearstatcache(true, $path);
                $newSize = is_file($path) ? filesize($path) : 0;
                if (! is_file($path)) {
                    $jpg = preg_replace('/\.[^.]+$/', '.jpg', $path);
                    if (is_file($jpg)) {
                        $path = $jpg;
                        $newSize = filesize($jpg);
                    }
                }
                $this->info("Compressed {$label}: {$humanSize} → ".round($newSize / 1024).' KB');
                $compressed++;
            } else {
                $this->warn("Failed: {$label} ({$humanSize})");
                $failed++;
            }
        }

        $this->newLine();
        $this->info(($dryRun ? 'Would compress' : 'Compressed').": {$compressed}, already OK: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
