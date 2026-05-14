{{-- Booking & review CTAs (uses $setting from the view / composer) --}}
@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
@endphp
<div class="ma-book-channels">
    <h4 class="section-title-sm font-weight-bold mb-3">Book your stay</h4>
    <div class="row g-2 mb-4">
        <div class="col-sm-6">
            <a class="theme-btn w-100 text-center d-block py-2" href="{{ route('room.booking') }}">Book direct</a>
        </div>
        @if (! empty(trim((string) ($setting->url_booking ?? ''))))
            <div class="col-sm-6">
                <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ $setting->url_booking }}" target="_blank" rel="noopener noreferrer">Booking.com</a>
            </div>
        @endif
        @if (! empty(trim((string) ($setting->url_expedia ?? ''))))
            <div class="col-sm-6">
                <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ $setting->url_expedia }}" target="_blank" rel="noopener noreferrer">Expedia</a>
            </div>
        @endif
        @if ($waDigits !== '' && strlen($waDigits) >= 8)
            <div class="col-sm-6">
                <a class="theme-btn w-100 text-center d-block py-2" href="https://wa.me/{{ $waDigits }}?text={{ rawurlencode('Hello, I would like to book a room at '.($setting->company ?? 'your hotel').'.') }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
            </div>
        @endif
        @if (! empty(trim((string) ($setting->email ?? ''))))
            <div class="col-sm-6">
                <a class="theme-btn style-three w-100 text-center d-block py-2" href="mailto:{{ $setting->email }}"><i class="far fa-envelope me-1"></i> Email</a>
            </div>
        @endif
    </div>

    @if (! empty(trim((string) ($setting->url_tripadvisor ?? ''))) || ! empty(trim((string) ($setting->url_google_business ?? ''))))
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
    @endif
</div>
