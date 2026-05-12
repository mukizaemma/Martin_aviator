@extends('layouts.frontbase')
<base href="/public">

@section('content')

@include('frontend.includes.page-header', [
    'title' => $room->roomName,
    'subtitle' => null,
    'imageUrl' => ! empty($room->image)
        ? asset('storage/images/rooms/'.$room->image)
        : null,
])

    <!-- Room Details Start -->
    <section class="product-details pt-100 rpt-70 rel z-1">
        <div class="container">
            <div class="row gap-90">
                <div class="col-lg-6">
                    <div class="product-details-images wow fadeInLeft delay-0-2s">
                        <!-- Preview Images -->
                        <div class="tab-content preview-images">
                            <!-- Main Service Image -->
                            <div class="tab-pane fade preview-item active show" id="preview1">
                                <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Preview">
                            </div>
                            
                            <!-- Gallery Images -->
                            @foreach ($images as $index => $image)
                                <div class="tab-pane fade preview-item" id="preview{{ $index + 2 }}">
                                    <img src="{{ asset('storage/images/rooms/' . $image->image) }}" alt="Preview">
                                </div>
                            @endforeach
                        </div>
            
                        <!-- Thumbnail Navigation -->
                        <div class="nav thumb-images rmb-20">
                            <!-- Main Service Image Thumbnail -->
                            <a href="#preview1" data-bs-toggle="tab" class="thumb-item active show">
                                <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Thumb">
                            </a>
            
                            <!-- Gallery Thumbnails -->
                            @foreach ($images as $index => $image)
                                <a href="#preview{{ $index + 2 }}" data-bs-toggle="tab" class="thumb-item">
                                    <img src="{{ asset('storage/images/rooms/' . $image->image) }}" alt="Thumb">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product-details-content mt-35 rmt-55 wow fadeInRight delay-0-2s">
                        <span class="price mb-30">{!! \App\Support\Currency::formatUsdHover($room->price) !!}</span>
                        <p>
                            {!! $room->description !!}
                        </p>
                        @if ($room->amenityOptions->isNotEmpty())
                            <div class="room-amenities pt-3">
                                <h5 class="mb-3">Amenities</h5>
                                <ul class="list-unstyled row row-cols-1 row-cols-md-2 g-2 small">
                                    @foreach ($room->amenityOptions as $am)
                                        <li class="col"><i class="fas fa-check text-warning me-2"></i>{{ $am->label }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {{-- <form action="#" class="add-to-cart pt-5">
                            <input type="number" value="01" min="1" max="20" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" required>
                            <button type="submit" class="theme-btn">Add to Cart</button>
                        </form> --}}
                        <ul class="category-tags pt-55 pb-40">
                            <li>
                                <b>Category</b>
                                <span>:</span>
                                <a href="#">{{ $room->category }}</a>
                            </li>
                        </ul>
                        {{-- <div class="social-style-three">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div> --}}
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
    <!-- Room Details End -->

    <div class="container py-5 mb-50">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10 col-sm-12" style="padding: 24px; background-color: #f9f9f9; color: #333; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <h3>Reserve this room now</h3>
                <form class="form" action="{{ route('reserveNow') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
                    @csrf
    
                    <div class="row">
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Check-in Date</label>
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <input type="date" name="checkin" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                        </div>
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Check-out Date</label>
                            <input type="date" name="checkout" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                        </div>
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Number of Guests</label>
                            <input type="number" name="adults" value="1" min="1" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                        <div class="col-xl-3 col-sm-12" style="margin-bottom: 12px;">
                            <label for="">Number of Rooms</label>
                            <input type="number" name="rooms" value="1" min="1" max="4" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                        </div>
                    </div>
                
                    <input type="text" name="names" placeholder="Your Names" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                    <input type="email" name="email" placeholder="Email" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                    <input type="text" name="phone" placeholder="Phone" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
                    <input type="text" name="address" placeholder="Address, Country, City" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px;">
    
                    <textarea name="description" placeholder="Any Special Request? (Optional)" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 12px; height: 80px;"></textarea>
    
                    <div class="row d-flex justify-content-center align-items-center gap-2" style="padding: 5px;">
                        <button type="submit" class="tp-btn btn btn-primary" style="width:20%; padding: 12px; border-radius: 4px; font-size: 1rem;">Submit</button>
                        <a href="{{route('rooms')}}" class="btn btn-secondary" style="width:40%; padding: 12px; border-radius: 4px; font-size: 1rem;">Back to all Rooms</a>
                    </div>
    
                </form>
            </div>
        </div>
    </div>
    

@endsection