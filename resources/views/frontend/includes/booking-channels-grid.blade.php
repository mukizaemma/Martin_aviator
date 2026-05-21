{{-- Booking channels — footer & book page (uses $setting) --}}
@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
    $waText = rawurlencode('Hello, I would like to book a room at '.($setting->company ?? 'your hotel').'.');
    $compact = $compact ?? false;
@endphp
<div class="ma-book-channels">
    @unless ($compact)
        <h4 class="section-title-sm font-weight-bold mb-3">Book your stay</h4>
    @endunless
    <p class="ma-book-channels__intro small text-muted mb-3">Choose how you would like to book — each option below opens the right next step.</p>

    <div class="ma-book-channels__group mb-4">
        <p class="ma-book-channels__group-title fw-semibold mb-2">Book at discounted price</p>
        <div class="row g-2">
            <div class="col-sm-6">
                <a class="theme-btn w-100 text-center d-block py-2" href="{{ route('pay.dpo') }}">
                    Book and pay directly
                    <span class="badge bg-warning text-dark ms-1">Coming soon</span>
                </a>
            </div>
            @if ($waDigits !== '' && strlen($waDigits) >= 8)
                <div class="col-sm-6">
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'whatsapp']) }}">
                        <i class="fab fa-whatsapp me-1"></i> Book through WhatsApp
                    </a>
                </div>
            @endif
            @if (! empty(trim((string) ($setting->email ?? ''))))
                <div class="col-sm-6">
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'email']) }}">
                        <i class="far fa-envelope me-1"></i> Book through email
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="ma-book-channels__group">
        <p class="ma-book-channels__group-title fw-semibold mb-2">Book with our trusted booking partners</p>
        <div class="row g-2">
            @if (! empty(trim((string) ($setting->url_booking ?? ''))))
                <div class="col-sm-6">
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'booking_com']) }}" target="_blank" rel="noopener noreferrer">Booking.com</a>
                </div>
            @endif
            @if (! empty(trim((string) ($setting->url_expedia ?? ''))))
                <div class="col-sm-6">
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'expedia']) }}">Expedia</a>
                </div>
            @endif
        </div>
    </div>

    @if (! $compact && (! empty(trim((string) ($setting->url_tripadvisor ?? ''))) || ! empty(trim((string) ($setting->url_google_business ?? '')))))
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <h4 class="section-title-sm font-weight-bold mb-3">Reviews</h4>
            <div class="row g-2">
                @if (! empty(trim((string) ($setting->url_tripadvisor ?? ''))))
                    <div class="col-sm-6">
                        <a class="btn btn-outline-light w-100" href="{{ $setting->url_tripadvisor }}" target="_blank" rel="noopener noreferrer">TripAdvisor</a>
                    </div>
                @endif
                @if (! empty(trim((string) ($setting->url_google_business ?? ''))))
                    <div class="col-sm-6">
                        <a class="btn btn-outline-light w-100" href="{{ $setting->url_google_business }}" target="_blank" rel="noopener noreferrer">Google</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
