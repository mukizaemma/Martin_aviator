<?php

namespace App\Http\Controllers;

use App\Models\DiningGalleryImage;
use App\Models\DiningMenuItem;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiningController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?? new Setting;
        $items = DiningMenuItem::orderBy('sort_order')->orderBy('title')->get();
        $gallery = DiningGalleryImage::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.dining.index', compact('setting', 'items', 'gallery'));
    }

    public function savePage(Request $request)
    {
        $setting = Setting::firstOrFail();

        DB::transaction(function () use ($request, $setting) {
            $setting->dining_intro = $request->input('dining_intro');

            if ($request->hasFile('dining_hero_image') && $request->file('dining_hero_image')->isValid()) {
                $path = $request->file('dining_hero_image')->store('public/images/pages');
                $setting->dining_hero_image = basename($path);
            }

            $setting->save();
        });

        return redirect()->route('diningMenu')->with('success', 'Dining page content saved.');
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price_usd' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images/dining');
            $imageName = basename($path);
        }

        $maxSort = (int) DiningMenuItem::max('sort_order');

        DiningMenuItem::create([
            'title' => $request->title,
            'price_usd' => $request->price_usd,
            'image' => $imageName,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('diningMenu')->with('success', 'Menu item added.');
    }

    public function updateMenuItem(Request $request, DiningMenuItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price_usd' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        $item->title = $request->title;
        $item->price_usd = $request->price_usd;

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::delete('public/images/dining/'.$item->image);
            }
            $path = $request->file('image')->store('public/images/dining');
            $item->image = basename($path);
        }

        $item->save();

        return redirect()->route('diningMenu')->with('success', 'Menu item updated.');
    }

    public function destroyMenuItem(DiningMenuItem $item)
    {
        if ($item->image) {
            Storage::delete('public/images/dining/'.$item->image);
        }
        $item->delete();

        return redirect()->route('diningMenu')->with('success', 'Menu item removed.');
    }

    public function storeGallery(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:6144',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('public/images/dining-gallery');
        $maxSort = (int) DiningGalleryImage::max('sort_order');

        DiningGalleryImage::create([
            'image' => basename($path),
            'caption' => $request->caption,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('diningMenu')->with('success', 'Gallery image added.');
    }

    public function destroyGallery(DiningGalleryImage $diningGalleryImage)
    {
        Storage::delete('public/images/dining-gallery/'.$diningGalleryImage->image);
        $diningGalleryImage->delete();

        return redirect()->route('diningMenu')->with('success', 'Gallery image removed.');
    }
}
