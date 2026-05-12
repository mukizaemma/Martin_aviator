@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Dining',
    'imageUrl' => ! empty($setting->dining_hero_image)
        ? asset('storage/images/pages/'.$setting->dining_hero_image)
        : null,
])

<section class="py-5 bg-white">
    <div class="container">
        @if (! empty($setting->dining_intro))
            <div class="row justify-content-center mb-5">
                <div class="col-lg-10 text-center">
                    <div class="section-title mb-4"><h2>Restaurant &amp; bar</h2></div>
                    <div class="lead text-muted">{!! $setting->dining_intro !!}</div>
                </div>
            </div>
        @endif

        <div class="row g-4 justify-content-center">
            @forelse ($menuItems as $item)
                <div class="col-md-6 col-lg-4 wow fadeInUp">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        @if ($item->image)
                            <div class="ratio ratio-4x3 bg-light">
                                <img src="{{ asset('storage/images/dining/'.$item->image) }}" alt="{{ $item->title }}" class="object-fit-cover w-100 h-100" style="object-fit: cover;">
                            </div>
                        @else
                            <div class="ratio ratio-4x3 bg-dark d-flex align-items-center justify-content-center text-white-50">No image</div>
                        @endif
                        <div class="card-body text-center">
                            <h4 class="card-title">{{ $item->title }}</h4>
                            <p class="mb-0 fs-5">{!! \App\Support\Currency::formatUsdHover($item->price_usd) !!}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">Menu coming soon.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
