@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Terms & conditions',
    'subtitle' => 'Booking policies and payment information',
    'imageUrl' => ! empty($about->middleImage ?? null)
        ? asset('storage/images/gallery/' . ltrim($about->middleImage, '/'))
        : null,
])

        <!-- Terms Area start -->
        @include('frontend.layouts.terms')
        <!-- Terms Area end -->
        


@endsection