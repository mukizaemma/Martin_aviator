@php
    $amenitiesBg = null;
    if (! empty($setting->flexible_stay_bg_image ?? null)) {
        $amenitiesBg = asset('storage/images/pages/' . ltrim($setting->flexible_stay_bg_image, '/'));
    } elseif (! empty($setting->facilities_hero_image ?? null)) {
        $amenitiesBg = asset('storage/images/pages/' . ltrim($setting->facilities_hero_image, '/'));
    }

    $items = [
        [
            'emoji' => '✈️',
            'title' => 'Airport Transfers',
            'text' => 'Enjoy complimentary airport pick-up and drop-off, just minutes from Kigali International Airport.',
            'href' => route('airportTransfer'),
        ],
        [
            'emoji' => '🍽️',
            'title' => 'Restaurant & Bar',
            'text' => 'Discover delicious meals, refreshing drinks, and a relaxing dining experience.',
            'href' => route('dining'),
        ],
        [
            'emoji' => '☕',
            'title' => 'Coffee Corner',
            'text' => 'Freshly brewed coffee available anytime for our coffee-loving guests.',
            'href' => route('dining'),
        ],
        [
            'emoji' => '🌿',
            'title' => 'Garden & Relaxation',
            'text' => 'Unwind in our peaceful outdoor garden and enjoy Kigali\'s fresh air.',
            'href' => route('facilities') . '#garden',
        ],
        [
            'emoji' => '🍳',
            'title' => 'Kitchen Facilities',
            'text' => 'Selected rooms include kitchen access for long stays and family convenience.',
            'href' => route('rooms'),
        ],
        [
            'emoji' => '🛋️',
            'title' => 'Shared Living Spaces',
            'text' => 'Perfect for families and groups who want to stay connected and comfortable.',
            'href' => route('rooms'),
        ],
        [
            'emoji' => '⚽',
            'title' => 'Sports Lounge',
            'text' => 'Watch live soccer and enjoy entertainment in a lively atmosphere.',
            'href' => route('facilities') . '#sports',
        ],
        [
            'emoji' => '📶',
            'title' => 'Free Wi-Fi',
            'text' => 'Stay connected with fast and reliable internet throughout the hotel.',
            'href' => route('facilities') . '#wifi',
        ],
        [
            'emoji' => '🅿️',
            'title' => 'Secure Parking',
            'text' => 'Safe and convenient parking available for all our guests.',
            'href' => route('contact'),
        ],
    ];
@endphp

<section class="amenities-band parallax-bg rel z-1" @if($amenitiesBg) style="background-image: url('{{ $amenitiesBg }}');" @endif aria-labelledby="amenities-band-heading">
    <div class="container container-1130 rel z-2">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="section-title text-center mb-45 wow fadeInUp delay-0-2s">
                    <h2 id="amenities-band-heading">Hotel Facilities</h2>
                    <p class="amenities-band-lead mt-20 mb-0">
                        At Martin Aviator Hotel, every facility is designed to make your stay more comfortable, convenient, and enjoyable.
                        Explore our key amenities below and click any facility to learn more about what we offer.
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center amenities-band-grid">
            @foreach ($items as $item)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <a href="{{ $item['href'] }}" class="amenities-band-card wow fadeInUp delay-0-2s text-decoration-none d-block h-100">
                        <div class="amenities-band-card-head">
                            <span class="amenities-band-emoji" aria-hidden="true">{{ $item['emoji'] }}</span>
                            <h3 class="amenities-band-card-title">{{ $item['title'] }}</h3>
                        </div>
                        <p class="amenities-band-card-text mb-0">{{ $item['text'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
