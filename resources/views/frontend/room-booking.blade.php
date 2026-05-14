@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Book a stay',
    'subtitle' => 'Tell us your dates and how you would like to pay or complete the booking. Your details are stored for the hotel team and match what you send by WhatsApp or email.',
    'imageUrl' => null,
])

<section class="ma-room-booking py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                <form method="post" action="{{ route('room.booking.store') }}" class="ma-room-booking__form border rounded-3 p-4 p-md-4 bg-light shadow-sm">
                    @csrf

                    <h3 class="h5 mb-3">Stay details</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="rb-room">Room (optional)</label>
                            <select class="form-select" id="rb-room" name="room_id">
                                <option value="">Let the hotel suggest</option>
                                @foreach ($rooms as $r)
                                    <option value="{{ $r->id }}" @selected((int) ($selectedRoomId ?? 0) === (int) $r->id)>{{ $r->roomName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rb-checkin">Check-in</label>
                            <input type="date" class="form-control" id="rb-checkin" name="check_in" value="{{ old('check_in') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rb-checkout">Check-out</label>
                            <input type="date" class="form-control" id="rb-checkout" name="check_out" value="{{ old('check_out') }}" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="1" id="rb-pickup" name="airport_pickup" @checked(old('airport_pickup'))>
                                <label class="form-check-label" for="rb-pickup">Airport pickup</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" value="1" id="rb-dropoff" name="airport_dropoff" @checked(old('airport_dropoff'))>
                                <label class="form-check-label" for="rb-dropoff">Airport drop-off</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="rb-extra">Additional requests</label>
                            <textarea class="form-control" id="rb-extra" name="additional_requests" rows="3" placeholder="Flight number, late arrival, celebration, accessibility…">{{ old('additional_requests') }}</textarea>
                        </div>
                    </div>

                    <h3 class="h5 mb-3">Your contact</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="rb-name">Full name</label>
                            <input type="text" class="form-control" id="rb-name" name="guest_name" value="{{ old('guest_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="rb-phone">Phone</label>
                            <input type="text" class="form-control" id="rb-phone" name="guest_phone" value="{{ old('guest_phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="rb-email">Email</label>
                            <input type="email" class="form-control" id="rb-email" name="guest_email" value="{{ old('guest_email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="rb-country">Country</label>
                            <input type="text" class="form-control" id="rb-country" name="guest_country" value="{{ old('guest_country') }}" required>
                        </div>
                    </div>

                    <h3 class="h5 mb-3">How would you like to complete this booking?</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="ma-room-booking__option border rounded-3 p-3 h-100 d-block bg-white">
                                <input type="radio" name="fulfillment_choice" value="direct_pay" class="form-check-input me-2" @checked(old('fulfillment_choice') === 'direct_pay')>
                                <strong>Direct pay</strong>
                                <span class="d-block small text-muted mt-1">Secure card payment via DPO will be available here soon. You will land on the payment information page.</span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="ma-room-booking__option border rounded-3 p-3 h-100 d-block bg-white">
                                <input type="radio" name="fulfillment_choice" value="pay_on_delivery" class="form-check-input me-2" @checked(old('fulfillment_choice', 'pay_on_delivery') === 'pay_on_delivery')>
                                <strong>Pay on arrival</strong>
                                <span class="d-block small text-muted mt-1">We save your request. On the next screen, choose WhatsApp or email to send it to the hotel.</span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="ma-room-booking__option border rounded-3 p-3 h-100 d-block bg-white">
                                <input type="radio" name="fulfillment_choice" value="booking_com" class="form-check-input me-2" @checked(old('fulfillment_choice') === 'booking_com')>
                                <strong>Booking.com</strong>
                                <span class="d-block small text-muted mt-1">We open your property listing in a new tab. Add this site’s dates in Booking.com’s flow.</span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="ma-room-booking__option border rounded-3 p-3 h-100 d-block bg-white">
                                <input type="radio" name="fulfillment_choice" value="expedia" class="form-check-input me-2" @checked(old('fulfillment_choice') === 'expedia')>
                                <strong>Expedia</strong>
                                <span class="d-block small text-muted mt-1">We open Expedia in a new tab. Complete booking there.</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2">
                        <p class="small text-muted mb-0">By continuing you agree your request may be stored for hotel operations.</p>
                        <button type="submit" class="theme-btn">Continue <i class="far fa-angle-right ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
