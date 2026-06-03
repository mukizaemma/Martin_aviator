@php
    $brandLogo = ! empty($setting->logo ?? null)
        ? asset('storage/images/' . ltrim($setting->logo, '/'))
        : asset('assets/images/martin-aviator-logo.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="{{ $setting->keywords ?? '' }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#e89b00">
    <meta name="spa-site-name" content="{{ e($setting->company ?? config('app.name')) }}">

    <!-- Title -->
    <title>{{ $setting->company ?? '' }}</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    
    <!-- Flaticon -->
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-5.14.0.min.css') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
    <!-- Nice Select -->
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.min.css') }}">
    <!-- Animate -->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <!-- Slick -->
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <!-- Main Style -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/brand-martin-aviator.css') }}">

</head>
<body class="home-one">
    <div class="page-wrapper">

        <!-- main header -->
        <header class="main-header">
           <div class="header-top-wrap bgc-primary">
               <div class="container">
                   <div class="header-top-single">
                       <div class="header-contact-inline">
                           <ul class="header-contact-list">
                               @if (! empty($setting->phone ?? null))
                               <li>
                                   <a href="tel:{{ preg_replace('/[^\d+]/', '', $setting->phone) }}" class="header-contact-link">{{ $setting->phone }}</a>
                               </li>
                               @endif
                               @if (! empty($setting->email ?? null))
                               <li>
                                   <a href="mailto:{{ $setting->email }}" class="header-contact-link">{{ $setting->email }}</a>
                               </li>
                               @endif
                           </ul>
                       </div>
                       <p class="header-airport-tagline">
                           2 minutes walk from the Kigali international airport exit gate.
                       </p>
                       <div class="header-social-inline">
                           <div class="social-style-two">
                               @if (! empty($setting->facebook ?? null))
                               <a href="{{ $setting->facebook }}" target="_blank" rel="noopener noreferrer">Facebook</a>
                               @endif
                               @if (! empty($setting->instagram ?? null))
                               <a href="{{ $setting->instagram }}" target="_blank" rel="noopener noreferrer">Instagram</a>
                               @endif
                               @if (! empty($setting->twitter ?? null))
                               <a href="{{ $setting->twitter }}" target="_blank" rel="noopener noreferrer">Twitter</a>
                               @endif
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           
            <!--Header-Upper-->
            <div class="header-upper">
                <div class="container clearfix">

                    <div class="header-inner header-inner--balanced rel align-items-center">
                        <div class="logo-outer">
                            <div class="logo"><a href="{{route('home')}}"><img src="{{ $brandLogo }}" alt="{{ $setting->company ?? 'Martin Aviator Hotel' }}" title="{{ $setting->company ?? '' }}" style="height: 60px !important"></a></div>
                        </div>

                        <div class="nav-outer clearfix">
                            <!-- Main Menu -->
                            <nav class="main-menu navbar-expand-lg">
                                <div class="navbar-header">
                                   <div class="mobile-logo my-15">
                                       <a href="{{route('home')}}">
                                            <img src="{{ $brandLogo }}" alt="{{ $setting->company ?? 'Martin Aviator Hotel' }}" title="{{ $setting->company ?? '' }}" style="height: 60px !important">
                                       </a>
                                   </div>
                                   
                                    <!-- Toggle Button -->
                                    <button type="button" class="navbar-toggle" data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                </div>

                                <div class="navbar-collapse collapse clearfix">

                                    <ul class="navigation clearfix">
                                        <li><a href="{{ route('home') }}">Home</a></li>
                                        <li class="dropdown"><a href="{{ route('rooms') }}">Accommodation</a>
                                            <ul>
                                                <li><a href="{{ route('rooms', ['tab' => 'rooms']) }}">Rooms</a></li>
                                                <li><a href="{{ route('rooms', ['tab' => 'apartments']) }}">Apartments</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ route('dining') }}">Bar &amp; Restaurant</a></li>
                                        <li><a href="{{ route('facilities') }}">Hotel Facilities</a></li>
                                        <li><a href="{{ route('gallery') }}">Gallery</a></li>
                                        <li><a href="{{ route('contact') }}">Contact</a></li>
                                        <li class="dropdown"><a href="{{ route('aboutUs') }}">About</a>
                                            <ul>
                                                <li><a href="{{ route('services') }}">Our Services</a></li>
                                                <li><a href="{{ route('aboutUs') }}">Our Team</a></li>
                                                <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                                                <li><a href="{{ route('blogs') }}">Updates</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>

                            </nav>
                            <!-- Main Menu End-->
                        </div>
                        
                        
                        <!-- Menu Button -->
                        <div class="menu-btns">
                           <a href="{{ route('room.booking') }}" class="theme-btn style-three" data-no-spa>Book Now <i class="far fa-angle-right"></i></a>
                           
                            <!-- menu sidbar -->
                            {{-- <div class="menu-sidebar">
                                <button>
                                    <img src="assets/images/icons/sidebar-toggler-color.png" alt="Toggler">
                                </button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <!--End Header Upper-->
        </header>
       
       
        <!--Form Back Drop-->
        <div class="form-back-drop"></div>

    <div class="container-fluid" id="spa-content" data-spa-container>
        @fragment('spa-main')
        @yield('content')
        @endfragment
    </div>

        @unless (request()->routeIs('room.booking', 'room.booking.confirmation', 'room.booking.email', 'room.booking.ota') || View::hasSection('hide_amenities_band'))
            @include('frontend.includes.amenities-band')
        @endunless
       
        <!-- footer area start -->
        <footer class="main-footer bgc-black pt-100 rel z-1 ma-footer-gold">
            <div class="container">
                <div class="row justify-content-xl-between justify-content-between g-4">
                    <div class="col-lg-4 col-sm-12">
                        <div class="footer-widget widget_about wow fadeInUp delay-0-2s">
                            <div class="footer-logo mb-25">
                                <a href="{{route('home')}}"><img src="{{ $brandLogo }}" alt="{{ $setting->company ?? 'Martin Aviator Hotel' }}" style="height: 80px !important"></a>
                            </div>
                            <p>
                                Close to the airport, designed for international guests — comfort, calm, and a genuine welcome after every journey.
                            </p>

                            <ul class="contact-list">
                                <li><i class="fas fa-phone-alt"></i> <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}">{{ $setting->phone ?? '' }}</a></li>
                                <li><i class="fas fa-envelope"></i> <a href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? '' }}</a></li>
                            </ul>
                            <div class="social-style-one pt-10">
                                <a href="{{ $setting->facebook ?? '' }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="{{ $setting->instagram ?? '' }}" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="{{ $setting->twitter ?? '' }}" target="_blank"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <div class="footer-widget widget_nav_menu wow fadeInUp delay-0-4s">
                            <h4 class="footer-title">Hotel Facilities</h4>
                            <ul class="list-style-one">
                                @foreach ($facilities as $facility )
                                <li><a href="{{ route('facilitySingle',['slug'=>$facility->slug]) }}">{{ $facility->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        @include('frontend.includes.booking-channels-grid', ['compact' => false])
                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <h4 class="section-title-sm font-weight-bold mb-3">Payment methods</h4>
                            <img src="{{ asset('assets/images/payment1.png') }}" alt="Accepted cards and mobile money" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom bgd-dark mt-40 pt-20 pb-5 rpt-25">
                <div class="container">
                   <div class="row">
                       <div class="col-lg-6">
                            <div class="copyright-text">
                                <p>©  <script>document.write(new Date().getFullYear())</script> <a href="{{route('home')}}">{{ $setting->company }}</a> All Rights Reserved.</p>
                            </div>
                       </div>
                       <div class="col-lg-6 text-lg-end">
                           <ul class="footer-bottom-nav rpb-10">
                               <li><a href="https://iremetech.com" target="_blank">Developed by Ireme Technologies</a></li>
                               <li><a  target="_blank"></a></li>

                           </ul>
                       </div>
                   </div>
                </div>
            </div>
        </footer>
        <!-- footer area end -->
        
        
        @include('frontend.includes.whatsapp-float')

        <!-- Scroll Top Button -->
        <button class="scroll-top scroll-to-target" data-target="html"><span class="fas fa-angle-double-up"></span></button>

    </div>
    <!--End pagewrapper-->
   
    
    <!-- Jquery -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}" defer></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}" defer></script>
    <!-- Slick (home hero + carousels) -->
    <script src="{{ asset('assets/js/slick.min.js') }}" defer></script>
    <!-- Nice Select -->
    <script src="{{ asset('assets/js/jquery.nice-select.min.js') }}" defer></script>
    <!-- Image Loader (gallery masonry) -->
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}" defer></script>
    <!-- Isotope -->
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}" defer></script>
    <!--  WOW Animation -->
    <script src="{{ asset('assets/js/wow.min.js') }}" defer></script>
    <!-- Custom script -->
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
    <script src="{{ asset('assets/js/dual-currency.js') }}" defer></script>
    <script src="{{ asset('assets/js/booking-room-persist.js') }}" defer></script>
    <script src="{{ asset('assets/js/parallax-bg.js') }}" defer></script>
    <script src="{{ asset('assets/js/ma-spa-nav.js') }}" defer></script>
    <script src="{{ asset('assets/js/gallery-page.js') }}" defer></script>
    @stack('frontend-scripts')

</body>
</html>