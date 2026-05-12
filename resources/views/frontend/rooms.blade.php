@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Our rooms',
    'subtitle' => $setting->flexible_stay_subheading ?: 'Choose the right setup for your trip.',
    'highlights' => [
        [
            'title' => $setting->flexible_stay_card1_title ?: 'Flexible Room Choices',
            'text' => $setting->flexible_stay_card1_text ?: 'Choose the room size and setup that fits your stay.',
        ],
        [
            'title' => $setting->flexible_stay_card2_title ?: 'Optional Kitchen Access',
            'text' => $setting->flexible_stay_card2_text ?: 'Available for guests who prefer cooking or long-term stays.',
        ],
        [
            'title' => $setting->flexible_stay_card3_title ?: 'Perfect for Families & Groups',
            'text' => $setting->flexible_stay_card3_text ?: 'Combine rooms and share living spaces comfortably.',
        ],
    ],
    'imageUrl' => ! empty($about->chooseusImage ?? null)
        ? asset('storage/images/gallery/' . ltrim($about->chooseusImage, '/'))
        : null,
])

@include('frontend.layouts.rooms')

@endsection