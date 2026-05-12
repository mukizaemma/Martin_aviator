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
        <section class="hero-slider-two rel z-1" style="width: min(1320px, calc(100% - 30px)); margin: 0 auto;">
            <div class="slider-two-active">
                @foreach ($slides as $slide )
                <div class="slider-item-two parallax-bg" style="background-image: url('{{asset('storage/images/slides').$slide->image}}'); object-fit: contain;">
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


        <!-- About Area start -->
        <section class="activity-area pt-30 rpt-50 pb-10 rpb-20 rel z-1">
            <div class="container">
            <section class="about-area-three pb-10 rpb-95 rel">
                <div class="container" style="width: min(1320px, calc(100% - 30px)); margin: 0 auto; text-align: center;">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-12">
                            <div class="about-content-three rmb-55 wow fadeInLeft delay-0-2s">
                                <div class="section-title mb-10">
                                    <h2>Welcome to Martin Aviator Hotel</h2>
                                    <p>{!! $about->welcome ?? '' !!}</p>
                                </div>
                                {{-- <a href="{{ route('rooms') }}" class="theme-btn" 
                                   style="display: inline-block; padding: 12px 25px; background-color: #000000; color: #fff; border-radius: 5px; 
                                          box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); transition: all 0.3s ease-in-out; text-decoration: none;">
                                    Reserve Your Room Now <i class="far fa-angle-right"></i>
                                </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            </div>
        </section>
        <!-- About Area end -->
        

        <!-- Room Area start -->
        <section class="room-area-three rooms-on-white py-130 rpy-100 rel z-1 bgc-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="section-title text-center mb-60 rmb-40 wow fadeInUp delay-0-2s">
                            <h2>Explore Our Luxury Trendy Rooms and Suites</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="room-two-active">
                    @foreach ($rooms as $room)
                    <div class="room-two-item wow fadeInUp delay-0-2s">
                        <div class="image">
                            <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Room">
                        </div>
                        <div class="content">
                            <h3><a href="room-details.html">{{ $room->roomName }}</a></h3>
                            <ul class="blog-meta">
                                <li>
                                    <i class="far fa-bed-alt"></i>
                                    <a href="#">Adults : {{ $room->maxAdults }}</a>
                                </li>
                                <div style="display: flex; align-items: center; gap: 20px; font-size: 16px; color: #333; font-weight: 500;">
                                    <i class="fas fa-bed brand-accent-icon" style="font-size: 20px;"></i> Bed
                                    <i class="fas fa-coffee brand-accent-icon" style="font-size: 20px; margin-left: 15px;"></i> Breakfast
                                </div>
                                
                            </ul>
                            <div class="price">
                                <b>{!! \App\Support\Currency::formatUsdHover($room->price) !!}</b>/<br>
                                <span>per night</span>
                            </div>

                            
                            <a href="{{ route('singleRoom',['slug'=>$room->slug]) }}" class="theme-btn" 
                                   style="display: inline-block; padding: 12px 25px; background-color: #000000; color: #fff; border-radius: 5px; 
                                          box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2); transition: all 0.3s ease-in-out; text-decoration: none;">
                                    View Details <i class="far fa-angle-right"></i>
                                </a>
                        </div>
                    </div>
                    @endforeach


                </div>
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

        @include('frontend.layouts.facilities')
        <!-- Food Drink Area start -->
        <section class="food-drink-area pt-30 rpt-100 pb-30 rpb-130">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5">

                        <div class="faq-content-part wow fadeInLeft delay-0-2s">
                            <div class="section-title mb-30">
                                <h2>Why Choosing Us</h2>
                            </div>
                            <div class="accordion" id="faq-accordion">
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            01. Exquisite Dining Experience
                                        </button>
                                    </h5>
                                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
                                            <p>
                                                Enjoy a variety of delicious local and international cuisines, 
                                            freshly prepared by our expert chefs to satisfy your taste buds
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            02. Affordable Luxury with Stunning Views
                                        </button>
                                    </h5>
                                    <div id="collapseTwo" class="accordion-collapse collapse show" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
                                            <p>
                                                Experience top-notch hospitality at competitive prices, 
                                            all while enjoying breathtaking views of Lake Kivu and the surrounding mountains
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            03. Convenient Transport Services
                                        </button>
                                    </h5>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
                                            <p>
                                                We offer seamless airport transfers and reliable local transport, 
                                            ensuring a stress-free journey from arrival to departure
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                            04. Best Tour & Boat Service Recommendations
                                        </button>
                                    </h5>
                                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
                                            <p>
                                                Explore the beauty of Kibuye with our expertly guided tours and recommended boat agencies for an unforgettable adventure on Lake Kivu
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                            05. Privacy & Security Guaranteed
                                        </button>
                                    </h5>
                                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
                                            <p>
                                                Enjoy a peaceful stay with 24/7 security and well-maintained private spaces, ensuring comfort and safety throughout your visit

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="food-drink-image rel wow fadeInUp delay-0-4s">
                            @if (! empty($about->aboutImage))
                                <img src="{{ asset('storage/images/gallery/' . ltrim($about->aboutImage, '/')) }}" alt="Food Restaurant" loading="lazy" decoding="async">
                            @else
                                <div class="bgc-black r-10" style="min-height: 320px; border-radius: 8px;" role="img" aria-label="Restaurant"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Food Drink Area end -->
                
        {{-- @include('frontend.includes.gallery') --}}

        <!-- Gallery Area start -->
        {{-- @include('frontend.layouts.gallery') --}}
        <!-- Gallery Area end -->
        
    @endsection