{{-- Booking channels — footer & book page (uses $setting) --}}
@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
    $compact = $compact ?? false;
@endphp
<div class="ma-book-channels" data-no-spa>
    @unless ($compact)
        <h4 class="section-title-sm font-weight-bold mb-3">Book your stay</h4>
    @endunless
    <p class="ma-book-channels__intro small text-muted mb-3">Choose how you would like to book — each option below opens the right next step.</p>

    <div class="ma-book-channels__group mb-3">
        <p class="ma-book-channels__group-title fw-semibold mb-2">Book at discounted price</p>
        <div class="ma-book-channels__list d-flex flex-column gap-2">
            <a class="ma-book-channels__btn theme-btn" href="{{ route('room.booking') }}" data-no-spa>
                <span class="ma-book-channels__btn-text">Book and pay directly</span>
                <span class="badge bg-warning text-dark ma-book-channels__badge">Coming soon</span>
            </a>
            @if ($waDigits !== '' && strlen($waDigits) >= 8)
                <a class="ma-book-channels__btn theme-btn style-three" href="{{ route('room.booking', ['channel' => 'whatsapp']) }}">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <span class="ma-book-channels__btn-text">Book through WhatsApp</span>
                </a>
            @endif
            @if (! empty(trim((string) ($setting->email ?? ''))))
                <a class="ma-book-channels__btn theme-btn style-three" href="{{ route('room.booking', ['channel' => 'email']) }}">
                    <i class="far fa-envelope" aria-hidden="true"></i>
                    <span class="ma-book-channels__btn-text">Book through email</span>
                </a>
            @endif
        </div>
    </div>

    <div class="ma-book-channels__group">
        <p class="ma-book-channels__group-title fw-semibold mb-2">Book with our trusted booking partners</p>
        <div class="ma-book-channels__list d-flex flex-column gap-2">
            @if (! empty(trim((string) ($setting->url_booking ?? ''))))
                <a class="ma-book-channels__btn theme-btn style-three" href="{{ route('room.booking', ['channel' => 'booking_com']) }}">Booking.com</a>
            @endif
            @if (! empty(trim((string) ($setting->url_expedia ?? ''))))
                <a class="ma-book-channels__btn theme-btn style-three" href="{{ route('room.booking', ['channel' => 'expedia']) }}">Expedia</a>
            @endif
        </div>
    </div>

    @if (! $compact && (! empty(trim((string) ($setting->url_tripadvisor ?? ''))) || ! empty(trim((string) ($setting->url_google_business ?? '')))))
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <h4 class="section-title-sm font-weight-bold mb-3">Reviews</h4>
            <div class="ma-book-channels__list d-flex flex-column gap-2">
                @if (! empty(trim((string) ($setting->url_tripadvisor ?? ''))))
                    <a class="ma-book-channels__btn btn btn-outline-light btn-sm" href="{{ $setting->url_tripadvisor }}" target="_blank" rel="noopener noreferrer">TripAdvisor</a>
                @endif
                @if (! empty(trim((string) ($setting->url_google_business ?? ''))))
                    <a class="ma-book-channels__btn btn btn-outline-light btn-sm" href="{{ $setting->url_google_business }}" target="_blank" rel="noopener noreferrer">Google</a>
                @endif
            </div>
        </div>
    @endif
</div>
