@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Terms & conditions',
    'subtitle' => 'Booking policies and payment information',
    'pageHeaderSlug' => 'terms',
])

        <!-- Terms Area start -->
        @include('frontend.layouts.terms')
        <!-- Terms Area end -->
        


@endsection