{{-- Floating WhatsApp — general enquiries (uses $setting from layout) --}}
@php
    $waDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?? ''));
    $waMessage = 'Hello, I have a general enquiry about '.($setting->company ?? 'the hotel').'.';
@endphp
@if (strlen($waDigits) >= 8)
    <a
        href="https://wa.me/{{ $waDigits }}?text={{ rawurlencode($waMessage) }}"
        class="ma-whatsapp-float"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat on WhatsApp for general enquiries"
        title="General enquiries on WhatsApp"
        data-no-spa
    >
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
        {{-- <span class="ma-whatsapp-float__label"></span> --}}
    </a>
@endif
