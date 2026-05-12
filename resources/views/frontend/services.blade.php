@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Our services',
    'subtitle' => null,
    'imageUrl' => null,
])

    @include('frontend.layouts.services')

    @include('frontend.layouts.gallery')

@endsection
