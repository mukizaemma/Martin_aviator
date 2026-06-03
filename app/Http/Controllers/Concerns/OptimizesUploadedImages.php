<?php

namespace App\Http\Controllers\Concerns;

use App\Services\ImageUploadOptimizer;
use Illuminate\Http\UploadedFile;

trait OptimizesUploadedImages
{
    protected function storeOptimizedImage(?UploadedFile $file, string $directory, string $preset = 'general', ?string $disk = null): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        return app(ImageUploadOptimizer::class)->store($file, $directory, $preset, $disk);
    }
}
