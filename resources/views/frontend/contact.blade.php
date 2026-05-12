@extends('layouts.frontbase')

@section('content')

    @include('frontend.includes.page-header', [
        'title' => 'Get in touch',
        'subtitle' => null,
        'imageUrl' => ! empty($about->middleImage ?? null)
            ? asset('storage/images/gallery/' . ltrim($about->middleImage, '/'))
            : null,
    ])

        <!-- Contact Form Area start -->
        <section id="airport-transfer" class="contact-page-area py-100 rpy-80 rel z-1" tabindex="-1">
            <div class="container">
                <div class="row">
                    @if (session()->has('success'))
                        <div class="arlert alert-success">
                            <button class="close" type="button" data-dismiss="alert">X</button>
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="arlert alert-danger">
                            <button class="close" type="button" data-dismiss="alert">X</button>
                            {{ session()->get('error') }}
                        </div>
                    @endif
                </div>

                <div class="row justify-content-center mb-55">
                    <div class="col-xl-8 col-lg-10 text-center">
                        <div class="section-title mb-10 wow fadeInUp delay-0-2s">
                            <span class="sub-title mb-15">Contact us</span>
                            <h2>Need help booking a room?</h2>
                            <p class="mb-0 text-muted">Reach us by phone, email, or the form below — we reply as soon as we can.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 justify-content-center mb-60 wow fadeInUp delay-0-2s">
                    <div class="col-md-4">
                        <div class="contact-info-item justify-content-center flex-column text-center border rounded-3 py-4 px-3 h-100 bg-white shadow-sm">
                            <div class="icon mx-auto mb-15">
                                <i class="flaticon-location-1"></i>
                            </div>
                            <div class="content">
                                <span class="title d-block mb-1">Our location</span>
                                <span class="text">{{ $setting->address }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info-item justify-content-center flex-column text-center border rounded-3 py-4 px-3 h-100 bg-white shadow-sm">
                            <div class="icon mx-auto mb-15">
                                <i class="flaticon-email-marketing"></i>
                            </div>
                            <div class="content">
                                <span class="title d-block mb-1">Email</span>
                                <span class="text">
                                    <a href="mailto:{{ $setting->email }}">{{ $setting->email }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info-item justify-content-center flex-column text-center border rounded-3 py-4 px-3 h-100 bg-white shadow-sm">
                            <div class="icon mx-auto mb-15">
                                <i class="flaticon-call"></i>
                            </div>
                            <div class="content">
                                <span class="title d-block mb-1">Phone</span>
                                <span class="text">
                                    <a href="tel:{{ preg_replace('/\s+/', '', $setting->phone ?? '') }}">{{ $setting->phone }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-10">
                        <div class="contact-page-form wow fadeInUp delay-0-2s">
                            <div class="section-title mb-15 text-center">
                                <h3>Send us a message</h3>
                                <p class="mb-0">Your email address will not be published. Required fields are marked *</p>
                            </div>

                            <form id="contactForm" class="contactForm" action="{{ route('sendMessage') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row gap-20 pt-15">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="name" name="names" class="form-control" value="" placeholder="Full name" required data-error="Please enter your name">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="phone_number" name="phone" class="form-control" value="" placeholder="Phone" required data-error="Please enter your Phone">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="email" id="email" name="email" class="form-control" value="" placeholder="Email" required data-error="Please enter your Email">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="subject" name="subject" class="form-control" value="" placeholder="Subject" required data-error="Please enter your Subject">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea name="message" id="message" class="form-control" rows="3" placeholder="Message" required data-error="Please enter your Message"></textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group pt-5 mb-0 text-center">
                                            <button type="submit" class="theme-btn">Send message<i class="far fa-arrow-right"></i></button>
                                            <div id="msgSubmit" class="hidden"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
        <!-- Contact Form Area end -->


        <!-- Location Map Area Start -->
        <div class="contact-page-map pb-120 rpb-90 wow fadeInUp delay-0-2s">
            <div class="container-fluid">
                <div class="our-location">
                    @if (! empty($setting->google_map_embed))
                        {!! $setting->google_map_embed !!}
                    @else
                        <iframe src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d1282.6733953538446!2d29.34723022219446!3d-2.0577636813635376!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x19dd291561adb953%3A0x6084750be3aaab83!2sDelta%20Resort%20Hotel%20Kibuye!3m2!1d-2.0575487!2d29.348358299999997!5e1!3m2!1sen!2srw!4v1738753640971!5m2!1sen!2srw" width="600" height="450" style="border:0;width: 100%;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    @endif
                </div>
            </div>
        </div>
        <!-- Location Map Area End -->


@endsection
