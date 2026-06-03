@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Booking received',
    'subtitle' => $booking->payment_timing === 'pay_direct'
        ? 'Your reservation is saved. Payment will be completed when card processing is active.'
        : 'Your reservation is saved. Complete the next step to send your request to the hotel.',
    'pageHeaderSlug' => 'booking',
])

<section class="ma-room-booking ma-room-booking--confirmation py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif

                <div class="ma-room-booking__summary border rounded-3 p-4 bg-light shadow-sm mb-4">
                    <h3 class="h6 text-uppercase text-muted mb-3">Your request</h3>
                    <dl class="row mb-0 ma-room-booking__dl">
                        <dt class="col-sm-4">Guest</dt>
                        <dd class="col-sm-8">{{ $booking->names }}</dd>
                        <dt class="col-sm-4">Stay</dt>
                        <dd class="col-sm-8">
                            {{ $booking->checkin->format('D j M Y') }}
                            <span class="text-muted">→</span>
                            {{ $booking->checkout->format('D j M Y') }}
                        </dd>
                        @if ($booking->room)
                            <dt class="col-sm-4">Room</dt>
                            <dd class="col-sm-8">{{ $booking->room->roomName }}</dd>
                        @endif
                        <dt class="col-sm-4">Payment</dt>
                        <dd class="col-sm-8">
                            {{ $booking->payment_timing === 'pay_direct' ? 'Book and pay directly' : 'Book and pay at the hotel' }}
                        </dd>
                        <dt class="col-sm-4">Confirmed via</dt>
                        <dd class="col-sm-8">
                            @if ($booking->confirmation_channel === 'whatsapp')
                                WhatsApp
                            @elseif ($booking->confirmation_channel === 'email')
                                Email
                            @else
                                Card
                            @endif
                        </dd>
                        @if ($booking->total > 0)
                            <dt class="col-sm-4">Estimated total</dt>
                            <dd class="col-sm-8">${{ number_format((float) $booking->total, 2) }}</dd>
                        @endif
                    </dl>
                </div>

                <div class="ma-room-booking__next border rounded-3 p-4 bg-white shadow-sm">
                    @if ($booking->confirmation_channel === 'whatsapp')
                        <p class="mb-4">Tap below to open WhatsApp with your stay details pre-filled.</p>
                        <a class="theme-btn" href="{{ route('room.booking.whatsapp', $booking->public_id) }}">
                            <i class="fab fa-whatsapp me-2"></i> Open WhatsApp
                        </a>
                    @elseif ($booking->confirmation_channel === 'email')
                        <p class="mb-4">Tap below to open your email app with the booking request ready to send.</p>
                        <a class="theme-btn" href="{{ route('room.booking.email', $booking->public_id) }}">
                            <i class="far fa-envelope me-2"></i> Open email
                        </a>
                    @else
                        <p class="mb-0">We have recorded your card payment request. The hotel team will confirm your reservation shortly.</p>
                    @endif
                    <hr class="my-4">
                    <p class="small text-muted mb-0">Reference: <code class="ma-room-booking__ref">{{ $booking->public_id }}</code></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
