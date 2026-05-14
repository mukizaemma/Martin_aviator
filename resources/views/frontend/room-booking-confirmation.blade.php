@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Almost done',
    'subtitle' => 'Your booking request is saved. Send it to the hotel with the channel you prefer.',
    'imageUrl' => null,
])

<section class="py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif
                <div class="border rounded-3 p-4 bg-light shadow-sm">
                    <p class="mb-4">Choose <strong>WhatsApp</strong> for a quick chat with the team, or <strong>Email</strong> to open your mail app with the same details pre-filled.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a class="theme-btn text-center" href="{{ route('room.booking.whatsapp', $booking->public_id) }}"><i class="fab fa-whatsapp me-2"></i> Send via WhatsApp</a>
                        <a class="theme-btn style-three text-center" href="{{ route('room.booking.email', $booking->public_id) }}"><i class="far fa-envelope me-2"></i> Send via email</a>
                    </div>
                    <hr class="my-4">
                    <p class="small text-muted mb-0">Reference: <code>{{ $booking->public_id }}</code></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
