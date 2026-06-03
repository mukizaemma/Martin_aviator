@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Gallery',
    'subtitle' => null,
    'pageHeaderSlug' => 'gallery',
])

        <!-- Gallery Area start -->
        @include('frontend.includes.gallery')
        
        <!-- Gallery Area end -->



@endsection
