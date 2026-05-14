<section class="rooms-2columns-area rooms-on-white pb-30 rpb-90 rel z-2">
    <div class="container container-1130">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="section-title text-center mb-70 rmb-50 wow fadeInUp delay-0-2s">
                    <h2>Our Stunning Rooms</h2>
                </div>
            </div>
        </div>
        <div class="row gap-90">
            @foreach($rooms as $room )
            <div class="col-md-6">
                <div class="room-item style-three wow fadeInUp delay-0-2s">
                    <div class="image">
                        <img src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="Room">
                    </div>
                    <div class="content">
                        <div class="price">{!! \App\Support\Currency::formatUsdWithLocal($room->price, $room->price_rwf) !!} Per Night</div>
                        <h3><a href="{{ route('singleRoom',['slug'=>$room->slug]) }}">{{ $room->roomName }}</a></h3>
                        <ul class="blog-meta">
                            <li>
                                <i class="far fa-drafting-compass"></i>
                                <a href="{{ route('singleRoom',['slug'=>$room->slug]) }}">Size : {{ $room->size }}</a>
                            </li>
                            <li>
                                <i class="far fa-bath"></i>
                                <a href="{{ route('singleRoom',['slug'=>$room->slug]) }}">Max Adults : {{ $room->maxAdults }}</a>
                            </li>
                            <li>
                                <i class="far fa-bed-alt"></i>
                                <a href="{{ route('singleRoom',['slug'=>$room->slug]) }}">Max Children : {{ $room->maxChildren }}</a>
                            </li>
                        </ul>
                        <a class="theme-btn style-two" href="{{ route('room.booking', ['room' => $room->slug]) }}">Book Now <i class="fal fa-angle-right"></i></a>
                    </div>
                </div>
            </div>                        
            @endforeach
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="section-title text-center mb-70 rmb-50 wow fadeInUp delay-0-2s">
                    <p>
                        Each of our rooms is designed for comfort and relaxation, offering stunning views, modern amenities, and a peaceful ambiance. 
                        Breakfast is included with all rooms, and unless stated otherwise, each room accommodates two guests. An extra bed is available upon request for $20 per night.
                    </p>
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

