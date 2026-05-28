@extends('layouts.frontbase')

    @section('content')
        
        <!-- Hidden Sidebar -->
        <section class="hidden-bar">
            <div class="inner-box text-center">
                <div class="cross-icon"><span class="fa fa-times"></span></div>
                <div class="title">
                    <h4>Get Appointment</h4>
                </div>

                <!--Appointment Form-->
                <div class="appointment-form">
                    <form method="post" action="contact.html">
                        <div class="form-group">
                            <input type="text" name="text" value="" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" value="" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <textarea placeholder="Message" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="theme-btn">Submit now</button>
                        </div>
                    </form>
                </div>

                <!--Social Icons-->
                <div class="social-style-one">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest-p"></i></a>
                </div>
            </div>
        </section>
        <!--End Hidden Sidebar -->
       

        <!-- Slider Section Start -->
        <section class="hero-slider-two hero-slider-two--edge rel z-1">
            <div class="slider-two-active">
                @foreach ($slides as $slide )
                <div class="slider-item-two parallax-bg" style="background-image: url('{{asset('storage/images/slides').$slide->image}}');">
                    <div class="container">
                        <div class="slider-content-two">
                            <h1>{{ $slide->heading }}</h1>
                            {{-- <p>We’re Awards Winning Hotel Agency & we’ve 25+ Years Of Experience</p> --}}
                            <a href="{{ route('rooms') }}" class="theme-btn">Explore Our Rooms <i class="far fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            </section>
            <!-- Slider Section End -->


        @php
            $welcomeImage = ! empty($about?->aboutImage)
                ? asset('storage/images/gallery/' . ltrim($about->aboutImage, '/'))
                : null;
        @endphp
        <!-- Welcome (below hero) -->
        <section class="home-welcome-section rel z-1 py-50 rpy-40">
            <div class="container-fluid home-welcome-container px-3 px-sm-4 px-lg-5">
                <div class="row align-items-center g-3 g-lg-4">
                    @if ($welcomeImage)
                        <div class="col-lg-5 wow fadeInLeft delay-0-2s">
                            <figure class="home-welcome-media mb-0">
                                <img src="{{ $welcomeImage }}" alt="Martin Aviator Hotel" loading="lazy" width="640" height="480">
                            </figure>
                        </div>
                    @endif
                    <div class="col-12 col-lg-{{ $welcomeImage ? '7' : '12' }} wow fadeInUp delay-0-2s">
                        <div class="home-welcome-content">
                            <h2 class="home-welcome-title">Welcome to Martin Aviator Hotel</h2>
                            <ul class="home-welcome-highlights" aria-hidden="true">
                                <li><i class="fas fa-plane" aria-hidden="true"></i><span>Airport proximity</span></li>
                                <li><i class="fas fa-bed" aria-hidden="true"></i><span>Comfortable stays</span></li>
                                <li><i class="fas fa-concierge-bell" aria-hidden="true"></i><span>Warm hospitality</span></li>
                            </ul>
                            <div class="home-welcome-body welcome-prose">
                                {!! $about?->welcome ?? '' !!}
                            </div>
                            <a href="{{ route('aboutUs') }}" class="theme-btn style-three home-welcome-cta mt-35">
                                View more <i class="far fa-angle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Welcome end -->
        

        <!-- Room Area start -->
        <section class="room-area-three rooms-on-white py-130 rpy-100 rel z-1 bgc-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="section-title text-center mb-60 rmb-40 wow fadeInUp delay-0-2s">
                            <h2>Explore Our Rooms &amp; Suites</h2>
                        </div>
                    </div>
                </div>
                <div class="row g-4 g-lg-4 home-rooms-grid-row justify-content-center">
                    @foreach ($rooms->take(4) as $room)
                        <div class="col-md-6 wow fadeInUp delay-0-2s">
                            <article class="room-two-item home-room-card h-100 d-flex flex-column">
                                <div class="image home-room-card__image">
                                    <img
                                        class="home-room-card__img"
                                        src="{{ asset('storage/images/rooms/' . $room->image) }}"
                                        alt="{{ $room->roomName }}"
                                        loading="lazy"
                                        width="800"
                                        height="500"
                                    >
                                </div>
                                <div class="content flex-grow-1 d-flex flex-column">
                                    <h3 class="mb-15"><a href="{{ route('singleRoom', ['slug' => $room->slug]) }}" data-ma-room-slug="{{ $room->slug }}">{{ $room->roomName }}</a></h3>
                                    <ul class="ma-room-inclusions list-unstyled small mb-2">
                                        <li><i class="fas fa-coffee me-1" aria-hidden="true"></i> Bed &amp; Breakfast</li>
                                        <li><i class="fas fa-shuttle-van me-1" aria-hidden="true"></i> Airport Shuttle</li>
                                    </ul>
                                    <div class="price">
                                        <b>{!! \App\Support\Currency::formatUsdOnly($room->price) !!}</b>
                                        <span class="d-block small">per night</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-auto">
                                        <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}" data-ma-room-slug="{{ $room->slug }}" class="theme-btn style-three home-room-card__btn flex-grow-1 d-inline-flex justify-content-center align-items-center">
                                            View Details <i class="far fa-angle-right"></i>
                                        </a>
                                        <a href="{{ route('pay.dpo', ['room' => $room->slug]) }}" data-ma-room-slug="{{ $room->slug }}" class="theme-btn home-room-card__btn flex-grow-1 d-inline-flex justify-content-center align-items-center">
                                            Book Now <i class="far fa-angle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                @if ($rooms->count() > 4)
                    <div class="text-center mt-50 wow fadeInUp delay-0-2s">
                        <a href="{{ route('rooms') }}" class="theme-btn style-three">
                            View all accommodation <i class="far fa-angle-right"></i>
                        </a>
                    </div>
                @endif
            </div>
            <div class="bg-lines">
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
            </div>
        </section>
        <!-- Room Area end -->

        @include('frontend.includes.flexible-stay')

        @include('frontend.includes.home-dining-choose-row')
                
        {{-- @include('frontend.includes.gallery') --}}

        <!-- Gallery Area start -->
        {{-- @include('frontend.layouts.gallery') --}}
        <!-- Gallery Area end -->
        
    @endsection