<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageHeaderController extends Controller
{
    public function index(): View
    {
        PageHeader::ensureDefaults();

        $headers = PageHeader::query()->orderBy('sort_order')->get();
        $definitions = collect(PageHeader::definitions())->keyBy('slug');

        return view('admin.page-headers', compact('headers', 'definitions'));
    }

    public function update(Request $request, PageHeader $pageHeader): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'nullable|image|max:8192',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_image')) {
            $pageHeader->clearImage();
        } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
            $pageHeader->storeUploadedImage($request->file('image'));
        }

        return redirect()
            ->route('pageHeaders')
            ->with('success', $pageHeader->label.' header updated.');
    }
}
