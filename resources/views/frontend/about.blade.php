@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'About us',
    'subtitle' => null,
    'imageUrl' => ! empty($about->aboutImage ?? null)
        ? asset('storage/images/gallery/' . ltrim($about->aboutImage, '/'))
        : null,
])

        <!-- Who We Ar Area start -->
        <section class="who-we-are-area pb-130 rpb-100 rel z-1">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xl-6 col-lg-7">
                        <div class="who-we-are-image rmb-55 wow fadeInUp delay-0-2s">
                            <img src="assets/images/about/who-we-are.jpg" alt="Who We Are">
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="who-we-are-content wow fadeInUp delay-0-4s">
                            <div class="section-title mb-35">
                                <h2>About Us</h2>
                                <p>
                                    {!! $about->welcome ?? '' !!}
                                </p>
                            </div>
                            {{-- <a class="theme-btn style-two" href="about.html">Get Started Us <i class="fal fa-angle-right"></i></a> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-lines for-bg-white">
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
               <span></span><span></span>
            </div>
        </section>
        <!-- Who We Ar Area end -->
        
        <!-- Services Area start -->
<!-- Terms and Conditions Section -->
@include('frontend.layouts.terms')


@endsection