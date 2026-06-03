<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadOptimizer
{
    /** @var array<string, array{width: int, height: int, cover: bool}> */
    private const PRESETS = [
        'slide' => ['width' => 1920, 'height' => 1080, 'cover' => true],
        'gallery' => ['width' => 1400, 'height' => 1050, 'cover' => true],
        'room' => ['width' => 1400, 'height' => 933, 'cover' => true],
        'facility' => ['width' => 1400, 'height' => 933, 'cover' => true],
        'service' => ['width' => 1400, 'height' => 933, 'cover' => true],
        'page' => ['width' => 1920, 'height' => 720, 'cover' => true],
        'dining' => ['width' => 1200, 'height' => 800, 'cover' => true],
        'partner' => ['width' => 900, 'height' => 450, 'cover' => true],
        'logo' => ['width' => 480, 'height' => 240, 'cover' => false],
        'general' => ['width' => 1600, 'height' => 1200, 'cover' => true],
    ];

    public function maxBytes(): int
    {
        return (int) config('media.max_image_bytes', 716800);
    }

    /**
     * Store an upload; optimize only when file size exceeds the limit.
     *
     * @param  string  $directory  e.g. public/images/gallery or images/menu-categories
     * @param  string|null  $disk  null = default (local), "public" for storage/app/public
     */
    public function store(UploadedFile $file, string $directory, string $preset = 'general', ?string $disk = null): string
    {
        $directory = trim($directory, '/');
        $diskName = $disk ?? config('filesystems.default', 'local');

        if ($file->getSize() <= $this->maxBytes()) {
            return $this->storeOriginal($file, $directory, $diskName);
        }

        $filename = $this->processAndSave($file->getRealPath(), $file->getMimeType(), $directory, $diskName, $preset);

        return $filename ?? $this->storeOriginal($file, $directory, $diskName);
    }

    /**
     * Compress an existing file on disk if it exceeds the limit. Returns true when file was rewritten.
     */
    public function compressPathIfNeeded(string $absolutePath, string $preset = 'general'): bool
    {
        if (! is_file($absolutePath)) {
            return false;
        }

        clearstatcache(true, $absolutePath);
        if (filesize($absolutePath) <= $this->maxBytes()) {
            return false;
        }

        if (! extension_loaded('gd')) {
            return false;
        }

        $mime = mime_content_type($absolutePath) ?: null;
        $image = $this->loadImage($absolutePath, $mime);
        if (! $image) {
            return false;
        }

        $presetConfig = self::PRESETS[$preset] ?? self::PRESETS['general'];
        $processed = $this->resizeToPreset($image, $presetConfig);
        imagedestroy($image);

        if (! $processed) {
            return false;
        }

        $flat = $this->flattenForJpeg($processed);
        imagedestroy($processed);

        $temp = $absolutePath.'.tmp.jpg';
        $this->saveUnderSizeLimit($flat, $temp);
        imagedestroy($flat);

        if (! is_file($temp) || filesize($temp) > $this->maxBytes()) {
            @unlink($temp);

            return false;
        }

        $newPath = preg_replace('/\.[^.]+$/', '', $absolutePath).'.jpg';
        @unlink($absolutePath);
        rename($temp, $newPath);

        return true;
    }

    /**
     * Save raw image bytes (e.g. from URL); compress when over the limit.
     */
    public function storeBinary(string $binary, string $directory, string $preset = 'general', ?string $disk = null, string $extension = 'jpg'): string
    {
        $directory = trim($directory, '/');
        $diskName = $disk ?? config('filesystems.default', 'local');
        $filename = $this->buildFilename($extension);
        $fullPath = $this->absolutePath($directory, $filename, $diskName);
        $this->ensureDirectory(dirname($fullPath));
        file_put_contents($fullPath, $binary);

        if (filesize($fullPath) <= $this->maxBytes()) {
            return $filename;
        }

        $presetConfig = self::PRESETS[$preset] ?? self::PRESETS['general'];
        $mime = mime_content_type($fullPath) ?: 'image/jpeg';
        $image = $this->loadImage($fullPath, $mime);

        if (! $image) {
            return $filename;
        }

        $processed = $this->resizeToPreset($image, $presetConfig);
        imagedestroy($image);

        if (! $processed) {
            return $filename;
        }

        $flat = $this->flattenForJpeg($processed);
        imagedestroy($processed);
        @unlink($fullPath);
        $jpgName = $this->buildFilename('jpg');
        $jpgPath = $this->absolutePath($directory, $jpgName, $diskName);
        $this->saveUnderSizeLimit($flat, $jpgPath);
        imagedestroy($flat);

        return $jpgName;
    }

    /**
     * @return string|null basename of saved file
     */
    private function processAndSave(string $sourcePath, ?string $mime, string $directory, string $diskName, string $preset): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $image = $this->loadImage($sourcePath, $mime);
        if (! $image) {
            return null;
        }

        $presetConfig = self::PRESETS[$preset] ?? self::PRESETS['general'];
        $processed = $this->resizeToPreset($image, $presetConfig);
        imagedestroy($image);

        if (! $processed) {
            return null;
        }

        $filename = $this->buildFilename('jpg');
        $fullPath = $this->absolutePath($directory, $filename, $diskName);
        $this->ensureDirectory(dirname($fullPath));

        $flat = $this->flattenForJpeg($processed);
        imagedestroy($processed);
        $this->saveUnderSizeLimit($flat, $fullPath);
        imagedestroy($flat);

        return $filename;
    }

    /**
     * @param  resource  $image
     * @return resource
     */
    private function flattenForJpeg($image)
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $flat = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($flat, 255, 255, 255);
        imagefill($flat, 0, 0, $white);
        imagecopy($flat, $image, 0, 0, 0, 0, $w, $h);

        return $flat;
    }

    private function storeOriginal(UploadedFile $file, string $directory, string $diskName): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = $this->buildFilename($extension);
        Storage::disk($diskName)->putFileAs($directory, $file, $filename);

        return $filename;
    }

    private function buildFilename(string $extension): string
    {
        $extension = preg_replace('/[^a-z0-9]/', '', $extension) ?: 'jpg';

        return Str::uuid()->toString().'.'.$extension;
    }

    private function absolutePath(string $directory, string $filename, string $diskName): string
    {
        return Storage::disk($diskName)->path($directory.'/'.$filename);
    }

    private function ensureDirectory(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * @param  resource  $image
     * @param  array{width: int, height: int, cover: bool}  $preset
     * @return resource|null
     */
    private function resizeToPreset($image, array $preset)
    {
        $srcW = imagesx($image);
        $srcH = imagesy($image);
        if ($srcW < 1 || $srcH < 1) {
            return null;
        }

        $maxW = $preset['width'];
        $maxH = $preset['height'];

        if ($preset['cover']) {
            $scale = max($maxW / $srcW, $maxH / $srcH);
            $tmpW = (int) max(1, round($srcW * $scale));
            $tmpH = (int) max(1, round($srcH * $scale));
            $tmp = imagecreatetruecolor($tmpW, $tmpH);
            imagecopyresampled($tmp, $image, 0, 0, 0, 0, $tmpW, $tmpH, $srcW, $srcH);

            $dst = imagecreatetruecolor($maxW, $maxH);
            $srcX = (int) max(0, floor(($tmpW - $maxW) / 2));
            $srcY = (int) max(0, floor(($tmpH - $maxH) / 2));
            imagecopy($dst, $tmp, 0, 0, $srcX, $srcY, $maxW, $maxH);
            imagedestroy($tmp);

            return $dst;
        }

        $scale = min($maxW / $srcW, $maxH / $srcH, 1);
        $dstW = (int) max(1, round($srcW * $scale));
        $dstH = (int) max(1, round($srcH * $scale));
        $dst = imagecreatetruecolor($dstW, $dstH);
        imagecopyresampled($dst, $image, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

        return $dst;
    }

    /**
     * @param  resource  $image
     */
    private function saveUnderSizeLimit($image, string $fullPath): void
    {
        $maxBytes = $this->maxBytes();
        $quality = 88;
        $current = $image;

        for ($attempt = 0; $attempt < 12; $attempt++) {
            imagejpeg($current, $fullPath, $quality);
            clearstatcache(true, $fullPath);
            $size = filesize($fullPath) ?: PHP_INT_MAX;

            if ($size <= $maxBytes) {
                if ($current !== $image) {
                    imagedestroy($current);
                }

                return;
            }

            if ($quality > 58) {
                $quality -= 6;

                continue;
            }

            $w = imagesx($current);
            $h = imagesy($current);
            $newW = (int) max(320, floor($w * 0.85));
            $newH = (int) max(240, floor($h * 0.85));
            $scaled = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($scaled, $current, 0, 0, 0, 0, $newW, $newH, $w, $h);

            if ($current !== $image) {
                imagedestroy($current);
            }
            $current = $scaled;
            $quality = 82;
        }

        imagejpeg($current, $fullPath, 75);
        if ($current !== $image) {
            imagedestroy($current);
        }
    }

    /**
     * @return resource|null
     */
    private function loadImage(string $path, ?string $mime)
    {
        $mime = $mime ?? @mime_content_type($path);

        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path) ?: null,
            'image/png' => @imagecreatefrompng($path) ?: null,
            'image/webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            'image/gif' => @imagecreatefromgif($path) ?: null,
            default => null,
        };
    }

    public static function presetForFolder(string $folderName): string
    {
        $map = config('media.folder_presets', []);

        return $map[$folderName] ?? 'general';
    }
}
