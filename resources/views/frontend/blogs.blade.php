@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'News & blog',
    'subtitle' => null,
    'imageUrl' => null,
])

@include('frontend.layouts.blogs')

@include('frontend.layouts.gallery')
@endsection
