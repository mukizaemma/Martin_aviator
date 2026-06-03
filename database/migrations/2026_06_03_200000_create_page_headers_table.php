<?php

use App\Models\About;
use App\Models\PageHeader;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_headers', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('label');
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $definitions = PageHeader::definitions();
        $about = About::query()->first();
        $setting = Setting::query()->first();

        foreach ($definitions as $sort => $def) {
            $image = null;
            switch ($def['slug']) {
                case 'about':
                    $image = $about?->aboutImage ? 'gallery/'.ltrim($about->aboutImage, '/') : null;
                    break;
                case 'contact':
                case 'terms':
                    $image = $about?->middleImage ? 'gallery/'.ltrim($about->middleImage, '/') : null;
                    break;
                case 'rooms':
                    $image = $about?->chooseusImage ? 'gallery/'.ltrim($about->chooseusImage, '/') : null;
                    break;
                case 'facilities':
                    $image = $setting?->facilities_hero_image ? 'pages/'.ltrim($setting->facilities_hero_image, '/') : null;
                    break;
                case 'dining':
                    $image = $setting?->dining_hero_image ? 'pages/'.ltrim($setting->dining_hero_image, '/') : null;
                    break;
            }

            PageHeader::query()->create([
                'slug' => $def['slug'],
                'label' => $def['label'],
                'image' => $image,
                'sort_order' => $sort,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_headers');
    }
};
