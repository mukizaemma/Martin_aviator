@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'News & blog',
    'subtitle' => null,
    'pageHeaderSlug' => 'blogs',
])

@include('frontend.layouts.blogs')

@include('frontend.layouts.gallery')
@endsection
