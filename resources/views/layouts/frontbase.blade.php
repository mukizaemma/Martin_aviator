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
    <meta name="theme-color" content="#e89b00">

    <!-- Title -->
    <title>{{ $setting->company ?? '' }}</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    
    <!-- Flaticon -->
    <link rel="stylesheet" href="assets/css/flaticon.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/css/fontawesome-5.14.0.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="assets/css/magnific-popup.min.css">
    <!-- Nice Select -->
    <link rel="stylesheet" href="assets/css/nice-select.min.css">
    <!-- Type Writer -->
    <link rel="stylesheet" href="assets/css/jquery.animatedheadline.css">
    <!-- Animate -->
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <!-- Slick -->
    <link rel="stylesheet" href="assets/css/slick.min.css">
    <!-- Main Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/brand-martin-aviator.css">

</head>
<body class="home-one">
    <div class="page-wrapper">

        <!-- Preloader -->
        <div class="preloader"></div>

        <!-- main header -->
        <header class="main-header">
           <div class="header-top-wrap bgc-primary">
               <div class="container">
                   <div class="header-top-single">
                       <div class="header-contact-inline">
                           <ul class="header-contact-list">
                               <li>
                                   <i class="fas fa-phone" aria-hidden="true"></i>
                                   <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}">{{ $setting->phone ?? '' }}</a>
                               </li>
                               <li>
                                   <i class="fas fa-envelope" aria-hidden="true"></i>
                                   <a href="mailto:{{ $setting->email ?? '' }}">{{ $setting->email ?? '' }}</a>
                               </li>
                           </ul>
                       </div>
                       <p class="header-airport-tagline">
                           <i class="fas fa-plane-departure" aria-hidden="true"></i>
                           <span>2 minutes walk from the Kigali international airport exit gate.</span>
                       </p>
                       <div class="header-social-inline">
                           <div class="social-style-two">
                               <a href="{{ $setting->facebook ?? '' }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                               <a href="{{ $setting->instagram ?? '' }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                               <a href="{{ $setting->twitter ?? '' }}" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
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
                                        <li><a href="{{ route('rooms') }}">Rooms &amp; Apartments</a></li>
                                        <li><a href="{{ route('dining') }}">Dining</a></li>
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
                           <a href="{{ route('rooms') }}" class="theme-btn style-three">Book Now <i class="far fa-angle-right"></i></a>
                           
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
        {{-- @show --}}
        @yield('content')
    </div>

        @include('frontend.includes.amenities-band')
       
        <!-- footer area start -->
        <footer class="main-footer bgc-black pt-100 rel z-1 ma-footer-gold">
            <div class="container">
                <div class="row justify-content-xl-between justify-content-between">
                    <div class="col-lg-3 col-sm-12">
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
                    <div class="col-lg-2 col-sm-12">
                        <div class="footer-widget widget_nav_menu wow fadeInUp delay-0-4s">
                            <h4 class="footer-title">Quick Links</h4>
                            <ul class="list-style-one">
                                <li><a href="{{ route('aboutUs') }}">About Us</a></li>
                                <li><a href="{{ route('rooms') }}">Our Rooms</a></li>
                                <li><a href="{{ route('facilities') }}">Our Services</a></li>
                                <li><a href="{{ route('contact') }}">Contact Us</a></li>
                                <li><a href="{{ route('terms') }}">Terms & Conditions</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-12">
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
                        <div class="col-lg-6">
                            <h4 class="section-title-sm font-weight-bold mb-6">Our Payment Methods</h4>
                            <img src="assets\images\payment1.png" alt="Visa Card, Master Card, Mobile Money, ....">
                            <a href="{{ route('rooms') }}" class="btn btn-outline-secondary mt-4">Book Now</a>
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
            <div class="bg-lines">
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
            </div>
        </footer>
        <!-- footer area end -->
        
        
        <!-- Scroll Top Button -->
        <button class="scroll-top scroll-to-target" data-target="html"><span class="fas fa-angle-double-up"></span></button>

    </div>
    <!--End pagewrapper-->
   
    
    <!-- Jquery -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}" defer></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}" defer></script>
    <!-- Appear Js -->
    <script src="{{ asset('assets/js/appear.min.js') }}" defer></script>
    <!-- Slick -->
    <script src="{{ asset('assets/js/slick.min.js') }}" defer></script>
    <!-- Magnific Popup -->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}" defer></script>
    <!-- Nice Select -->
    <script src="{{ asset('assets/js/jquery.nice-select.min.js') }}" defer></script>
    <!-- Image Loader -->
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}" defer></script>
    <!-- Calendar -->
    <script src="{{ asset('assets/js/calendar.global.min.js') }}" defer></script>
    <!-- Circle Progress -->
    <script src="{{ asset('assets/js/circle-progress.min.js') }}" defer></script>
    <!-- Isotope -->
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}" defer></script>
    <!--  WOW Animation -->
    <script src="{{ asset('assets/js/wow.min.js') }}" defer></script>
    <!-- Custom script -->
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
    <script src="{{ asset('assets/js/dual-currency.js') }}" defer></script>
    <script src="{{ asset('assets/js/parallax-bg.js') }}" defer></script>
    <script>
        (function () {
            var contentSelector = '[data-spa-container]';
            var content = document.querySelector(contentSelector);
            if (!content || !window.fetch || !window.history || !window.history.pushState) {
                return;
            }

            var prefetchCache = new Map();
            var inFlightController = null;

            function shouldHandleLink(link) {
                if (!link || !link.href) return false;
                if (link.target && link.target !== '_self') return false;
                if (link.hasAttribute('download')) return false;
                if ((link.getAttribute('rel') || '').includes('external')) return false;
                var url = new URL(link.href, window.location.origin);
                if (url.origin !== window.location.origin) return false;
                if (url.hash && url.pathname === window.location.pathname) return false;
                if (url.pathname.startsWith('/dashboard') || url.pathname.startsWith('/login') || url.pathname.startsWith('/register')) return false;
                return true;
            }

            function extractDocumentParts(htmlText) {
                var doc = new DOMParser().parseFromString(htmlText, 'text/html');
                var nextContent = doc.querySelector(contentSelector);
                if (!nextContent) return null;
                return {
                    title: doc.title || document.title,
                    bodyClass: doc.body ? doc.body.className : document.body.className,
                    contentHtml: nextContent.innerHTML
                };
            }

            function showLoadingState(isLoading) {
                document.body.classList.toggle('spa-loading', isLoading);
                var preloader = document.querySelector('.preloader');
                if (preloader) {
                    preloader.style.display = isLoading ? 'block' : 'none';
                }
            }

            function reRunFrontendScripts() {
                var scriptEl = document.querySelector('script[src*="assets/js/script.js"]');
                if (!scriptEl) return;
                var freshScript = document.createElement('script');
                freshScript.src = scriptEl.src;
                freshScript.defer = true;
                scriptEl.parentNode.replaceChild(freshScript, scriptEl);
            }

            function loadPage(url, pushState) {
                if (inFlightController) {
                    inFlightController.abort();
                }

                inFlightController = new AbortController();
                var requestUrl = url.toString();
                showLoadingState(true);

                var fetchPromise = prefetchCache.get(requestUrl) || fetch(requestUrl, {
                    signal: inFlightController.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }).then(function (response) {
                    if (!response.ok) throw new Error('Navigation failed');
                    return response.text();
                });

                return fetchPromise.then(function (htmlText) {
                    var parsed = extractDocumentParts(htmlText);
                    if (!parsed) {
                        window.location.href = requestUrl;
                        return;
                    }

                    content.innerHTML = parsed.contentHtml;
                    document.title = parsed.title;
                    document.body.className = parsed.bodyClass;
                    window.scrollTo(0, 0);

                    if (pushState) {
                        window.history.pushState({ spa: true, url: requestUrl }, '', requestUrl);
                    }

                    reRunFrontendScripts();
                    if (typeof window.initParallaxBackgrounds === 'function') {
                        window.initParallaxBackgrounds();
                    }
                }).catch(function (error) {
                    if (error.name === 'AbortError') return;
                    window.location.href = requestUrl;
                }).finally(function () {
                    showLoadingState(false);
                });
            }

            document.addEventListener('mouseover', function (event) {
                var link = event.target.closest('a[href]');
                if (!shouldHandleLink(link)) return;

                var href = new URL(link.href, window.location.origin).toString();
                if (prefetchCache.has(href)) return;

                prefetchCache.set(href, fetch(href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                }).then(function (response) {
                    if (!response.ok) throw new Error('Prefetch failed');
                    return response.text();
                }).catch(function () {
                    prefetchCache.delete(href);
                }));
            });

            document.addEventListener('click', function (event) {
                if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                var link = event.target.closest('a[href]');
                if (!shouldHandleLink(link)) return;

                event.preventDefault();
                var url = new URL(link.href, window.location.origin);
                loadPage(url, true);
            });

            window.addEventListener('popstate', function () {
                loadPage(new URL(window.location.href), false);
            });
        })();
    </script>

</body>
</html>