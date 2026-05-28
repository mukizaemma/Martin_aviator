{{--
    Shared inner-page header: optional hero background, otherwise solid bar (same pattern as Dining fallback).
    Pass: title (required), optional subtitle, optional imageUrl (full URL from asset()).
    Does not introduce new settings fields — callers pass URLs built from existing About/Setting/Facility/etc.
--}}
@php
    $__title = $title ?? ($pageTitle ?? 'Page');
    $__subtitle = $subtitle ?? null;
    $__img = $imageUrl ?? null;
    $__highlights = $highlights ?? [];
@endphp
@if ($__img)
<section class="page-banner-area page-banner-area--edge parallax-bg pt-170 rpt-110 pb-160 rpb-125 rel z-1 bgs-cover bgc-black text-center"
    style="background-image: url('{{ $__img }}'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="banner-inner text-white">
            <h1 class="page-title wow fadeInUp delay-0-2s">{{ $__title }}</h1>
            @if ($__subtitle)
                <p class="mb-0 wow fadeInUp delay-0-3s" style="color: rgba(255, 255, 255, 0.95); font-size: 1.08rem; line-height: 1.75; font-weight: 500; text-shadow: 0 2px 10px rgba(0,0,0,0.45);">{{ $__subtitle }}</p>
            @endif
            @if (! empty($__highlights))
                <div class="row g-3 justify-content-center mt-4">
                    @foreach ($__highlights as $item)
                        <div class="col-lg-4 col-md-6">
                            <div class="px-3 py-3 rounded h-100" style="background: rgba(0,0,0,0.45); border: 1px solid rgba(255,255,255,0.18);">
                                <h5 class="text-white mb-2">{{ $item['title'] ?? '' }}</h5>
                                <p class="mb-0" style="color: rgba(255, 255, 255, 0.92); font-size: 1rem; line-height: 1.65; font-weight: 500; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">{{ $item['text'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="bg-lines">
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
    </div>
</section>
@else
<section class="page-banner-area page-banner-area--edge page-banner-area--solid pt-100 rpt-70 pb-100 rpb-70 rel z-1 bgc-black text-center text-white">
    <div class="container py-3">
        <div class="banner-inner">
            <h1 class="page-title wow fadeInUp delay-0-2s">{{ $__title }}</h1>
            @if ($__subtitle)
                <p class="mb-0 wow fadeInUp delay-0-3s" style="color: rgba(255, 255, 255, 0.95); font-size: 1.08rem; line-height: 1.75; font-weight: 500; text-shadow: 0 2px 10px rgba(0,0,0,0.45);">{{ $__subtitle }}</p>
            @endif
            @if (! empty($__highlights))
                <div class="row g-3 justify-content-center mt-4">
                    @foreach ($__highlights as $item)
                        <div class="col-lg-4 col-md-6">
                            <div class="px-3 py-3 rounded h-100" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2);">
                                <h5 class="text-white mb-2">{{ $item['title'] ?? '' }}</h5>
                                <p class="mb-0" style="color: rgba(255, 255, 255, 0.92); font-size: 1rem; line-height: 1.65; font-weight: 500; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">{{ $item['text'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="bg-lines">
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
        <span></span><span></span>
    </div>
</section>
@endif
