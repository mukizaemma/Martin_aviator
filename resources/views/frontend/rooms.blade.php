@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Accommodation',
    'subtitle' => $setting->flexible_stay_subheading ?: 'Rooms and apartments near Kigali International Airport.',
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
    'pageHeaderSlug' => 'rooms',
])

@include('frontend.layouts.rooms')

@endsection
