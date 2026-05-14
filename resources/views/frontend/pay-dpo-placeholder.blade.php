@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Direct payment',
    'subtitle' => 'Secure card payments through DPO will be connected here soon.',
    'imageUrl' => null,
])

<section class="py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="border rounded-3 p-4 bg-light">
                    <p class="mb-3">You chose <strong>direct pay</strong>. Your booking request has been recorded. When DPO is integrated, this page will collect card details securely.</p>
                    <p class="small text-muted mb-0">Until then, you can go back and submit the same request with <strong>Pay on arrival</strong> (WhatsApp or email) if you prefer.</p>
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="{{ route('room.booking') }}" class="theme-btn style-three">Edit booking request</a>
                        <a href="{{ route('contact') }}" class="theme-btn">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
