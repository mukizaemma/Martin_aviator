@extends('layouts.frontbase')

@section('content')

@php
    $roomsJson = $roomsPayload ?? collect();
    $selectedRoomSlug = $selectedRoomId ? ($rooms->firstWhere('id', $selectedRoomId)?->slug ?? '') : '';
@endphp

<section class="ma-bw py-80 rpy-60 bg-white rel z-1" id="ma-booking-wizard"
    data-rooms='@json($roomsJson)'
    data-selected-room-id="{{ (int) ($selectedRoomId ?? 0) }}"
    data-selected-room-slug="{{ $selectedRoomSlug }}"
    data-direct-pay="{{ $directPayEnabled ? '1' : '0' }}"
    data-channels-ready="{{ $bookingChannelsReady ? '1' : '0' }}">
    <div class="container">
        @if (! $bookingChannelsReady)
            <div class="alert alert-warning mb-4">
                Online booking is not available right now
                @if (! $whatsappAvailable)
                    (WhatsApp contact is not configured)
                @endif
                @if (! $emailAvailable)
                    (hotel email is not configured)
                @endif
                . Please <a href="{{ route('contact') }}">contact us</a> directly.
            </div>
        @endif

        <div id="bw-inline-error" class="alert alert-warning mb-3" role="alert" hidden></div>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <nav class="ma-bw__stepper" aria-label="Booking progress">
            <ol class="ma-bw__stepper-list">
                <li class="ma-bw__step ma-bw__step--active" data-step-indicator="1"><span class="ma-bw__step-num">1</span><span class="ma-bw__step-label">Stay</span></li>
                <li class="ma-bw__step" data-step-indicator="2"><span class="ma-bw__step-num">2</span><span class="ma-bw__step-label">Guest</span></li>
                <li class="ma-bw__step" data-step-indicator="3"><span class="ma-bw__step-num">3</span><span class="ma-bw__step-label">Submit</span></li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8">
                <form method="post" action="{{ route('room.booking.store') }}" id="ma-bw-form" class="ma-bw__form" @disabled(! $bookingChannelsReady)>
                    @csrf
                    <input type="hidden" name="line_items" id="ma-bw-line-items" value="{{ old('line_items', '[]') }}">

                    {{-- Step 1: Stay --}}
                    <div class="ma-bw__panel ma-bw__panel--active" data-bw-step="1">
                        <div class="ma-bw__card">
                            <header class="ma-bw__card-head">
                                <i class="far fa-calendar-alt" aria-hidden="true"></i>
                                <div>
                                    <h2 class="ma-bw__card-title">Your stay</h2>
                                    <p class="ma-bw__card-sub">Set dates and guests — add or change rooms below.</p>
                                </div>
                            </header>
                            <div class="ma-bw__stay-row">
                                <div class="ma-bw__field ma-bw__field--date">
                                    <label class="ma-bw__label" for="bw-checkin">Check-in</label>
                                    <input type="date" class="ma-bw__control form-control" id="bw-checkin" name="check_in" value="{{ old('check_in') }}" required>
                                    <span class="ma-bw__nights-badge" id="bw-nights-badge" hidden></span>
                                </div>
                                <div class="ma-bw__field ma-bw__field--date">
                                    <label class="ma-bw__label" for="bw-checkout">Check-out</label>
                                    <input type="date" class="ma-bw__control form-control" id="bw-checkout" name="check_out" value="{{ old('check_out') }}" required>
                                </div>
                                <div class="ma-bw__field ma-bw__field--num">
                                    <label class="ma-bw__label" for="bw-adults">Adults</label>
                                    <input type="number" class="ma-bw__control form-control" id="bw-adults" name="adults" min="1" max="20" value="{{ old('adults', 2) }}" required>
                                </div>
                                <div class="ma-bw__field ma-bw__field--num">
                                    <label class="ma-bw__label" for="bw-children">Children</label>
                                    <input type="number" class="ma-bw__control form-control" id="bw-children" name="children" min="0" max="20" value="{{ old('children', 0) }}">
                                </div>
                                <div class="ma-bw__field ma-bw__field--num">
                                    <label class="ma-bw__label" for="bw-room-count">Rooms</label>
                                    <input type="number" class="ma-bw__control form-control" id="bw-room-count" name="room_count" min="1" max="10" value="{{ old('room_count', 1) }}" required>
                                </div>
                            </div>
                            <div class="ma-bw__lines mt-4" id="bw-line-list" aria-live="polite"></div>
                            <div class="ma-bw__add-actions mt-3">
                                <button type="button" class="ma-bw__add-btn" id="bw-add-room" data-bs-toggle="modal" data-bs-target="#bw-room-modal">
                                    <i class="far fa-bed" aria-hidden="true"></i> Add room
                                </button>
                                <button type="button" class="ma-bw__add-btn ma-bw__add-btn--muted" id="bw-add-experience" disabled title="Coming soon">
                                    <i class="far fa-hiking" aria-hidden="true"></i> Add experience
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Guest --}}
                    <div class="ma-bw__panel" data-bw-step="2" hidden>
                        <div class="ma-bw__card">
                            <header class="ma-bw__card-head">
                                <i class="far fa-user" aria-hidden="true"></i>
                                <div>
                                    <h2 class="ma-bw__card-title">Guest details</h2>
                                    <p class="ma-bw__card-sub">We need your contact details to confirm your stay.</p>
                                </div>
                            </header>
                            <div class="ma-bw__guest-grid">
                                <div class="ma-bw__field">
                                    <label class="ma-bw__label" for="bw-name">Full name</label>
                                    <input type="text" class="ma-bw__control form-control" id="bw-name" name="guest_name" value="{{ old('guest_name') }}" required autocomplete="name">
                                </div>
                                <div class="ma-bw__field">
                                    <label class="ma-bw__label" for="bw-phone">Phone / WhatsApp</label>
                                    <input type="tel" class="ma-bw__control form-control" id="bw-phone" name="guest_phone" value="{{ old('guest_phone') }}" required autocomplete="tel">
                                </div>
                                <div class="ma-bw__field">
                                    <label class="ma-bw__label" for="bw-email">Email</label>
                                    <input type="email" class="ma-bw__control form-control" id="bw-email" name="guest_email" value="{{ old('guest_email') }}" required autocomplete="email">
                                </div>
                                <div class="ma-bw__field">
                                    <label class="ma-bw__label" for="bw-country">Country</label>
                                    <input type="text" class="ma-bw__control form-control" id="bw-country" name="guest_country" value="{{ old('guest_country') }}" required autocomplete="country-name">
                                </div>
                            </div>

                            <div class="ma-bw__field ma-bw__field--full mt-3">
                                <span class="ma-bw__label d-block">Airport transfers <span class="text-muted fw-normal">(optional)</span></span>
                                <div class="ma-bw__airport-row">
                                    <label class="ma-rb-airport ma-rb-airport--compact">
                                        <input class="ma-rb-airport__input" type="checkbox" value="1" name="airport_pickup" @checked(old('airport_pickup'))>
                                        <span class="ma-rb-airport__box">
                                            <span class="ma-rb-airport__check" aria-hidden="true"></span>
                                            <span class="ma-rb-airport__title">Airport pickup</span>
                                        </span>
                                    </label>
                                    <label class="ma-rb-airport ma-rb-airport--compact">
                                        <input class="ma-rb-airport__input" type="checkbox" value="1" name="airport_dropoff" @checked(old('airport_dropoff'))>
                                        <span class="ma-rb-airport__box">
                                            <span class="ma-rb-airport__check" aria-hidden="true"></span>
                                            <span class="ma-rb-airport__title">Airport drop-off</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="ma-bw__field ma-bw__field--full mt-3">
                                <label class="ma-bw__label" for="bw-extra">Special requests <span class="text-muted fw-normal">(optional)</span></label>
                                <textarea class="ma-bw__control form-control ma-bw__control--textarea" id="bw-extra" name="additional_requests" rows="3" placeholder="Flight number, late arrival, accessibility…">{{ old('additional_requests') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Review details, then payment / confirmation --}}
                    <div class="ma-bw__panel" data-bw-step="3" hidden>
                        <div class="ma-bw__card">
                            <header class="ma-bw__card-head">
                                <i class="far fa-calendar-check" aria-hidden="true"></i>
                                <div>
                                    <h2 class="ma-bw__card-title">Confirm your reservation</h2>
                                    <p class="ma-bw__card-sub" id="bw-submit-intro">Review your booking, then choose how you would like to pay.</p>
                                </div>
                            </header>

                            <section class="ma-bw__review-block" aria-labelledby="bw-review-heading">
                                <h3 class="ma-bw__section-title" id="bw-review-heading">Your booking</h3>
                                <div id="bw-review-body" class="ma-bw__review"></div>
                            </section>

                            @php
                                $paymentTiming = old('payment_timing', '');
                                $confirmationChannel = old('confirmation_channel', '');
                            @endphp

                            <section class="ma-bw__payment-block" id="bw-payment-section" aria-labelledby="bw-payment-heading">
                                <h3 class="ma-bw__section-title" id="bw-payment-heading">Complete your booking</h3>

                                <input type="hidden" name="payment_timing" id="bw-payment-timing" value="{{ $paymentTiming }}">
                                <input type="hidden" name="confirmation_channel" id="bw-confirmation-channel" value="{{ $confirmationChannel }}">

                                <div class="ma-bw__path-pick d-flex flex-wrap gap-2 mb-3">
                                    <button type="button" class="ma-bw__path-btn theme-btn {{ $paymentTiming === 'pay_direct' ? '' : 'style-three' }}" data-bw-path="pay_direct" id="bw-path-direct">
                                        Book and pay now
                                    </button>
                                    <button type="button" class="ma-bw__path-btn theme-btn {{ $paymentTiming === 'pay_at_hotel' ? '' : 'style-three' }}" data-bw-path="pay_at_hotel" id="bw-path-hotel">
                                        Book and pay at the hotel
                                    </button>
                                </div>

                                <div id="bw-panel-direct" class="ma-bw__path-panel" @if($paymentTiming !== 'pay_direct') hidden @endif>
                                    @if ($directPayEnabled)
                                        <p class="text-muted small mb-3">Enter your card details to pay and confirm online.</p>
                                        <div class="row g-2 ma-bw__card-form">
                                            <div class="col-md-6">
                                                <label class="ma-bw__label" for="bw-card-name">Name on card</label>
                                                <input type="text" class="ma-bw__control form-control" id="bw-card-name" name="card_name" autocomplete="cc-name" placeholder="As shown on card">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="ma-bw__label" for="bw-card-number">Card number</label>
                                                <input type="text" class="ma-bw__control form-control" id="bw-card-number" name="card_number" inputmode="numeric" autocomplete="cc-number" placeholder="0000 0000 0000 0000" maxlength="19">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="ma-bw__label" for="bw-card-exp">Expiry</label>
                                                <input type="text" class="ma-bw__control form-control" id="bw-card-exp" name="card_exp" placeholder="MM/YY" autocomplete="cc-exp" maxlength="5">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="ma-bw__label" for="bw-card-cvc">CVC</label>
                                                <input type="text" class="ma-bw__control form-control" id="bw-card-cvc" name="card_cvc" inputmode="numeric" autocomplete="cc-csc" maxlength="4">
                                            </div>
                                        </div>
                                        <p class="small text-muted mt-2 mb-0">Your card is processed securely.</p>
                                        <button type="submit" class="theme-btn mt-3" id="bw-submit-direct">
                                            Pay and confirm reservation <i class="far fa-angle-right ms-2"></i>
                                        </button>
                                    @else
                                        <p class="text-muted small mb-0">Online card payment is not available yet. Please choose <strong>Book and pay at the hotel</strong> below.</p>
                                    @endif
                                </div>

                                <div id="bw-panel-hotel" class="ma-bw__path-panel" @if($paymentTiming !== 'pay_at_hotel') hidden @endif>
                                    @include('frontend.partials.booking-hotel-confirm')
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="ma-bw__nav mt-4 d-flex flex-wrap gap-2 justify-content-between">
                        <button type="button" class="btn btn-secondary ma-bw__back" id="bw-back" hidden><i class="far fa-angle-left me-1"></i> Back</button>
                        <button type="button" class="theme-btn ms-auto" id="bw-continue">Continue <i class="far fa-angle-right ms-2"></i></button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <aside class="ma-bw__summary" aria-labelledby="bw-summary-title">
                    <div class="ma-bw__summary-head">
                        <h2 class="ma-bw__summary-title" id="bw-summary-title">Your stay summary</h2>
                    </div>
                    <div class="ma-bw__summary-body" id="bw-summary-body">
                        <p class="text-muted small mb-0">Add dates and a room to see your summary.</p>
                    </div>
                    <div class="ma-bw__summary-total">
                        <span>Estimated total</span>
                        <strong id="bw-summary-total">$0.00</strong>
                    </div>
                    <button type="button" class="theme-btn w-100 ma-bw__summary-cta" id="bw-summary-continue" disabled>Continue <i class="far fa-angle-right ms-2"></i></button>
                </aside>
            </div>
        </div>
    </div>

</section>

@push('frontend-scripts')
<script src="{{ asset('assets/js/booking-wizard.js') }}" defer></script>
@endpush

{{-- Add room modal (outside wizard — must not use gallery .modal CSS from style.css) --}}
<div class="modal fade" id="bw-room-modal" tabindex="-1" aria-labelledby="bw-room-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bw-room-modal-label">Add a room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="bw-modal-room">Room type</label>
                <select class="form-select" id="bw-modal-room">
                    <option value="">Select a room…</option>
                    @foreach ($rooms as $r)
                        <option value="{{ $r->id }}" data-slug="{{ $r->slug }}" data-price="{{ $r->price }}">
                            {{ $r->roomName }} — {{ \App\Support\Currency::formatRoomPriceLabel($r->price, $r->price_rwf) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="theme-btn" id="bw-modal-add">Add to stay</button>
            </div>
        </div>
    </div>
</div>
@endsection
