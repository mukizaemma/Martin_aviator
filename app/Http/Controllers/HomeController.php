<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Room;
use App\Models\User;
use App\Models\About;
use App\Models\Image;
use App\Models\Slide;
use App\Models\Booking;
use App\Models\Gallery;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Facility;
use App\Models\DiningGalleryImage;
use App\Models\DiningMenuItem;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function index()
{
    $slides = Slide::oldest()->get();
    $categories = Image::select('category')->distinct()->pluck('category');
    $images = Image::latest()->take(12)->get();
    $blogs = Blog::latest()->take(6)->get();
    $about = About::first();
    $setting = Setting::first();
    $users = User::select('id', 'name', 'email')->get(); // Select only necessary fields
    $rooms = Room::with('images')->oldest()->get();
    $services = Service::oldest()->get();
    $gallery = Image::latest()->paginate(12); // Use pagination to load in chunks
    $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);

    return view('frontend.index', compact(
        'about', 'images', 'categories', 'rooms', 'facilities', 
        'services', 'slides', 'gallery', 'users', 'setting', 'blogs'
    ));
}

    public function contact()
    {
        $rooms = Room::with('images')->oldest()->get();
        $setting = Setting::first();
        $about = About::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.contact',[
            'rooms' => $rooms,
            'setting' => $setting,
            'facilities' => $facilities,
            'about' => $about,
            ]);
    }

    public function terms()
    {
        $rooms = Room::with('images')->oldest()->get();
        $setting = Setting::first();
        $about = About::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.terms',[
            'rooms' => $rooms,
            'setting' => $setting,
            'facilities' => $facilities,
            'about' => $about,
            ]);
    }

    public function aboutUs()
    {
        $rooms = Room::with('images')->oldest()->get();
        $setting = Setting::first();
        $about = About::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.about',[
            'rooms' => $rooms,
            'setting' => $setting,
            'facilities' => $facilities,
            'about' => $about,
            ]);
    }


    public function facilities()
    {
        $rooms = Room::with('images')->oldest()->get();
        $facilities = Facility::orderBy('created_at', 'asc')->get();
        $setting = Setting::first();
        $gallery = Image::latest()->get();
        $diningGallery = DiningGalleryImage::orderBy('sort_order')->orderBy('id')->get();

        return view('frontend.facilities', [
            'facilities' => $facilities,
            'setting' => $setting,
            'rooms' => $rooms,
            'gallery' => $gallery,
            'diningGallery' => $diningGallery,
        ]);
    }

    public function dining()
    {
        $rooms = Room::with('images')->oldest()->get();
        $setting = Setting::first();
        $about = About::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        $menuItems = DiningMenuItem::orderBy('sort_order')->orderBy('title')->get();

        return view('frontend.dining', compact('rooms', 'setting', 'about', 'facilities', 'menuItems'));
    }

    
    public function facilitySingle($slug)
    {
        $rooms = Room::with('images')->oldest()->get();

        $facility = Facility::where('slug', $slug)->firstOrFail();
        $images = DB::table('facility_images')->where('facility_id',$facility->id)->latest()->get();
        $reletedFacilities = Facility::where('id','!=', $facility->id)->get();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        $setting = Setting::first();
        return view('frontend.facilitySingle',[
            'facility'=>$facility,
            'images'=>$images,
            'setting'=>$setting,
            'facilities'=>$facilities,
            'rooms'=>$rooms,
            'reletedFacilities'=>$reletedFacilities,
        ]);
    }

    public function services()
    {
        $rooms = Room::with('images')->oldest()->get();
        $services = Service::oldest()->get();
        $setting = Setting::first();
        $gallery = Image::latest()->get();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.services', [
            'services' => $services,
            'rooms' => $rooms,
            'facilities' => $facilities,
            'setting' => $setting,
            'gallery' => $gallery,
            ]);
    }

    
    public function singleService($slug)
    {

        $rooms = Room::with('images')->oldest()->get();
        $service = Service::where('slug', $slug)->firstOrFail();
        $images = DB::table('service_images')->where('service_id',$service->id)->latest()->get();
        $setting = Setting::first();
        $gallery = Image::latest()->get();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.serviceSingle',[
            'service'=>$service,
            'rooms'=>$rooms,
            'images'=>$images,
            'setting'=>$setting,
            'facilities'=>$facilities,
            'gallery'=>$gallery,
        ]);
    }


    public function gallery()
    {
        $rooms = Room::with('images')->oldest()->get();
        $categories = Image::select('category')->distinct()->pluck('category');
        $images = Image::latest()->get();
        $setting = Setting::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.gallery', [
            'images' => $images,
            'setting' => $setting,
            'rooms' => $rooms,
            'facilities' => $facilities,
            'categories' => $categories,
            ]);
    }

    public function blogs()
    {
        $rooms = Room::with('images')->oldest()->get();
        $gallery = Image::latest()->get();
        $setting = Setting::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        $blogs = Blog::latest()->paginate(9);

        return view('frontend.blogs', [
            'gallery' => $gallery,
            'setting' => $setting,
            'rooms' => $rooms,
            'facilities' => $facilities,
            'blogs' => $blogs,
        ]);
    }


    public function reservationPage()
    {
        // $reservation = Reservepolocy::all();
        $rooms = Room::all();
        return view('frontend.reservation', [
            // 'reservation' => $reservation,
            'rooms' => $rooms
        ]);
    }

    public function rooms()
    {

        $rooms = Room::with('images')->oldest()->get();
        $setting = Setting::first();
        $about = About::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.rooms',[
            'rooms' => $rooms,
            'setting' => $setting,
            'facilities' => $facilities,
            'about' => $about,
            ]);
    }

    public function singleRoom($slug)
    {

        $room = Room::with('amenityOptions')->where('slug', $slug)->firstOrFail();
        $images = DB::table('room_images')->where('room_id',$room->id)->paginate(3);
        $rooms = Room::with('images')->oldest()->get();
        $setting = Setting::first();
        $facilities = Facility::orderBy('created_at', 'asc')->paginate(6);
        return view('frontend.roomSingle', [
            'images' => $images,
            'room' => $room,
            'rooms' => $rooms,
            'setting' => $setting,
            'facilities' => $facilities,
        ]);
    }

    public function SendMessage(Request $request){
            
        $comment = Message::create([
            'names' => $request->input('names'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
        ]);
    
        if ($comment) {
            // Mail::to('mukizaemma34@gmail.com')->send(new BlogCommentsNotofications($comment));
            return redirect()->back()->with('success', 'Your Message is successfully submitted. We will get back to you soon!');
        }
    
        else{
            return redirect()->back()->with('error', 'Failed to add the comment. Please try again.');
        }
    }

    public function reserveNow(Request $request)
    {
        $today = now()->toDateString();
        $bookingCount = Booking::where('email', $request->email)
            ->whereDate('created_at', $today)
            ->count();
    
        if ($bookingCount >= 2) {
            return redirect()->back()->with('error', 'You cannot make more than 2 bookings in a day with the same email.');
        }
    
        $nights = now()->parse($request->checkout)->diffInDays($request->checkin);
    
        $room = Room::find($request->room_id);
        $price = $room ? $room->price : 0;
    
        $total = $nights * $price;
    
        $booking = Booking::create([
            'names' => $request->names,
            'phone' => $request->phone,
            'email' => $request->email,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'adults' => $request->adults,
            'rooms' => $request->rooms,
            'children' => $request->input('children', 0),
            'nights' => $nights,
            'total' => $total,
            'address' => $request->address,
            'description' => $request->description,
            'status' => 'Pending',
            'room_id' => $request->room_id,
        ]);
    if($booking){
        return redirect()->back()->with('success', 'Booking successfully created. Thank you!');
    }
    else{
        return redirect()->back()->with('error', 'Something went wrong. Please try again later');
    }
    
    }
    
    
    public function checkAvailability(Request $request)
    {
        $itemType = $request->input('item');
        $checkinDate = date('Y-m-d', strtotime($request->input('checkin')));
        $checkoutDate = date('Y-m-d', strtotime($request->input('checkout')));
    
        if ($itemType == 'Room') {
            $rooms = Room::with('roomBookings')->get();
    
            $availableRooms = collect(); // Initialize an empty collection
    
            $startDate = strtotime($checkinDate);
            $endDate = strtotime($checkoutDate);
    
            // Loop through each date within the selected date range
            while ($startDate <= $endDate) {
                $currentDate = date('Y-m-d', $startDate);
    
                // Check availability for each room on the current date
                foreach ($rooms as $room) {
                    $roomAvailability = $room->quantity;
    
                    // Get the bookings for the current date
                    $bookings = $room->roomBookings()
                        ->whereDate('checkin', '<=', $currentDate)
                        ->whereDate('checkout', '>=', $currentDate)
                        ->get();
    
                    // Subtract the booked quantity for the current date
                    foreach ($bookings as $booking) {
                        $roomAvailability -= $booking->room_quantity;
                    }
    
                    // If the room is available, add it to the available rooms collection
                    if ($roomAvailability > 0) {
                        $availableRooms->push([
                            'date' => $currentDate,
                            'room' => $room,
                        ]);
                    }
                }
    
                // Move to the next date
                $startDate = strtotime('+1 day', $startDate);
            }


            return view('frontend.availableRooms', compact('availableRooms', 'checkinDate', 'checkoutDate'));
        }

        elseif ($itemType == 'Table') {
            // Call function to check available rooms
            $availableTables = DB::table('tables')
                ->where('is_available', true)
                ->whereNotIn('id', function ($query) use ($checkinDate, $checkoutDate) {
                    $query->select('table_id')
                        ->from('tablebookings')
                        ->where(function ($query) use ($checkinDate, $checkoutDate) {
                            $query->where('checkin', '<', $checkoutDate)
                                ->where('checkout', '>', $checkinDate);
                        })
                        ->orWhere(function ($query) use ($checkinDate, $checkoutDate) {
                            $query->where('checkin', '=', $checkinDate)
                                ->where('checkout', '=', $checkoutDate);
                        })
                        ->pluck('table_id');
                })
                ->get();


            return view('frontend.availableTables', compact('availableTables', 'checkinDate', 'checkoutDate'));
        }
    }

    public function confirmReservation(Request $request, $id)
    {
        $room = Room::findOrFail($id);
    
        // Check if there are available rooms
        if ($room->quantity > 0) {
            $booking = new Roombooking();
            $booking->room_id = $room->id;
            $booking->names = $request->names;
            $booking->email = $request->email;
            $booking->phone = $request->phone;
            $booking->checkin = $request->checkin;
            $booking->checkout = $request->checkout;
            $booking->price = $request->price;
            $booking->room = $request->room;
            $booking->adults = $request->adults;
            $booking->description = $request->description;
            $booking->save();
    
            return redirect()->back()->with('success', 'Thank you for booking with us. Remember we can pick you from Kigali at only $10');
        } else {
            // No available rooms
            return redirect()->back()->with('error', 'Sorry, the selected room is no longer available.');
        }
    }
    


    public function confirmTableReservation(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $booking = new Tablebooking();
        $booking->table_id = $table->id;
        $booking->tableName = $table->name;
        $booking->names = $request->names;
        // $booking->email = $request->email;
        $booking->phone = $request->phone;
        $booking->checkin = $request->checkin;
        $booking->checkout = $request->checkout;
        $booking->people = $request->people;
        $booking->description = $request->description;
        $booking->save();

        // Update room availability based on booked dates
        $startDate = date('Y-m-d H:i:s', strtotime($request->checkin));
        $endDate = date('Y-m-d H:i:s', strtotime($request->checkout));

        DB::table('tables')
            ->where('id', $id)
            ->whereBetween('dateAvailable', [$startDate, $endDate])
            ->update(['is_available' => false]);

        return redirect()->back()->with('success', 'Thank you for booking with us. Remember we can pick you from Kigali at only $10');
    }


    public function restaurant()
    {

        $gallery = Facility::where('category', 'Restaurant')->get();
        return view('frontend.restaurant', ['gallery' => $gallery]);
    }

}
