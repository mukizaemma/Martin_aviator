<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\ImageUploadOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PageHeader extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'slug',
        'label',
        'image',
        'sort_order',
    ];

    /**
     * @return array<int, array{slug: string, label: string, path: string}>
     */
    public static function definitions(): array
    {
        return [
            ['slug' => 'about', 'label' => 'About', 'path' => '/about'],
            ['slug' => 'contact', 'label' => 'Contact', 'path' => '/contact'],
            ['slug' => 'terms', 'label' => 'Terms & conditions', 'path' => '/terms'],
            ['slug' => 'rooms', 'label' => 'Rooms & accommodation', 'path' => '/rooms'],
            ['slug' => 'gallery', 'label' => 'Gallery', 'path' => '/Gallery'],
            ['slug' => 'services', 'label' => 'Services', 'path' => '/services'],
            ['slug' => 'blogs', 'label' => 'Updates / blog', 'path' => '/blogs'],
            ['slug' => 'facilities', 'label' => 'Facilities', 'path' => '/facilities'],
            ['slug' => 'dining', 'label' => 'Dining', 'path' => '/dining'],
            ['slug' => 'booking', 'label' => 'Book a room', 'path' => '/book-room'],
        ];
    }

    public static function ensureDefaults(): void
    {
        foreach (self::definitions() as $sort => $def) {
            self::query()->firstOrCreate(
                ['slug' => $def['slug']],
                ['label' => $def['label'], 'sort_order' => $sort]
            );
        }
    }

    public static function urlFor(string $slug): ?string
    {
        $header = self::query()->where('slug', $slug)->first();
        if (! $header || ! $header->image) {
            return null;
        }

        return $header->publicUrl();
    }

    public function publicUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return asset('storage/images/'.ltrim($this->image, '/'));
    }

    public function storeUploadedImage(UploadedFile $file): void
    {
        if ($this->image) {
            $this->deleteStoredImage();
        }

        $filename = app(ImageUploadOptimizer::class)->store($file, 'public/images/pages', 'page');
        $this->image = 'pages/'.$filename;
        $this->save();
    }

    public function clearImage(): void
    {
        $this->deleteStoredImage();
        $this->image = null;
        $this->save();
    }

    protected function deleteStoredImage(): void
    {
        if (! $this->image) {
            return;
        }

        $relative = str_starts_with($this->image, 'pages/')
            ? 'public/images/'.$this->image
            : 'public/images/'.ltrim($this->image, '/');

        if (Storage::exists($relative)) {
            Storage::delete($relative);
        }
    }
}
