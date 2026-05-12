<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/',[App\Http\Controllers\HomeController::class,'index'])->name('home');
Route::get('/about-delta-resort-hotel',[App\Http\Controllers\HomeController::class,'aboutUs'])->name('aboutUs');
Route::get('/accommodation-at-delta-resort',[App\Http\Controllers\HomeController::class,'rooms'])->name('rooms');
Route::get('/rooms/{slug}',[App\Http\Controllers\HomeController::class,'singleRoom'])->name('singleRoom');
Route::get('/services',[App\Http\Controllers\HomeController::class,'services'])->name('services');
Route::get('/service/{slug}',[App\Http\Controllers\HomeController::class,'singleService'])->name('singleService');
Route::post('/reserveNow',[App\Http\Controllers\HomeController::class,'reserveNow'])->name('reserveNow');
Route::get('/reserve/{slug}',[App\Http\Controllers\HomeController::class,'reserveRoom'])->name('reserveRoom');
Route::post('/saveBookings',[App\Http\Controllers\HomeController::class,'saveBookings'])->name('saveBookings');
Route::get('/facilities',[App\Http\Controllers\HomeController::class,'facilities'])->name('facilities');
Route::get('/dining',[App\Http\Controllers\HomeController::class,'dining'])->name('dining');
Route::get('/facilities/{slug}',[App\Http\Controllers\HomeController::class,'facilitySingle'])->name('facilitySingle');
Route::get('/restaurant',[App\Http\Controllers\HomeController::class,'restaurant'])->name('restaurant');
Route::get('/Gallery',[App\Http\Controllers\HomeController::class,'gallery'])->name('gallery');
Route::get('/blogs',[App\Http\Controllers\HomeController::class,'blogs'])->name('blogs');
Route::get('/blogs/{slug}',[App\Http\Controllers\HomeController::class,'blog'])->name('blog');
Route::get('/Contact',[App\Http\Controllers\HomeController::class,'contact'])->name('contact');
Route::get('/terms',[App\Http\Controllers\HomeController::class,'terms'])->name('terms');

Route::get('/airport-transfer', function () {
    return redirect()->to(route('contact') . '#airport-transfer');
})->name('airportTransfer');

Route::post('/sendMessage',[App\Http\Controllers\HomeController::class,'sendMessage'])->name('sendMessage');
Route::post('/bookRoom/{id}',[App\Http\Controllers\HomeController::class,'bookRoom'])->name('bookRoom');

// Route::middleware(['normalUser'])->group(function () {
Route::get('/MyCart',[App\Http\Controllers\HomeController::class,'showCart'])->name('showCart');
Route::get('/MyCart/{id}',[App\Http\Controllers\HomeController::class,'removeFood'])->name('removeFood');
Route::post('/addCart/{id}',[App\Http\Controllers\HomeController::class,'addCart'])->name('addCart');
Route::post('confirmOrder',[App\Http\Controllers\HomeController::class,'confirmOrder'])->name('confirmOrder');



Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard',[App\Http\Controllers\AdminController::class,'index'])->name('dashboard');

    Route::get('/setting',[App\Http\Controllers\SettingController::class,'setting'])->name('setting');
    Route::post('/saveSetting',[App\Http\Controllers\SettingController::class,'saveSetting'])->name('saveSetting');
    Route::get('/about',[App\Http\Controllers\SettingController::class,'about'])->name('about');
    Route::post('/saveAbout',[App\Http\Controllers\SettingController::class,'saveAbout'])->name('saveAbout');

    Route::get('/dining-menu', [App\Http\Controllers\DiningController::class, 'index'])->name('diningMenu');
    Route::post('/dining-menu/page', [App\Http\Controllers\DiningController::class, 'savePage'])->name('diningMenu.page');
    Route::post('/dining-menu/items', [App\Http\Controllers\DiningController::class, 'storeMenuItem'])->name('diningMenu.items.store');
    Route::post('/dining-menu/items/{item}', [App\Http\Controllers\DiningController::class, 'updateMenuItem'])->name('diningMenu.items.update');
    Route::delete('/dining-menu/items/{item}', [App\Http\Controllers\DiningController::class, 'destroyMenuItem'])->name('diningMenu.items.destroy');
    Route::post('/dining-gallery', [App\Http\Controllers\DiningController::class, 'storeGallery'])->name('diningGallery.store');
    Route::delete('/dining-gallery/{diningGalleryImage}', [App\Http\Controllers\DiningController::class, 'destroyGallery'])->name('diningGallery.destroy');


    // Facilities
    Route::get('/roomType', [App\Http\Controllers\RoomsController::class, 'roomType'])->name('roomType');
    Route::post('/roomTypeCreate', [App\Http\Controllers\RoomsController::class, 'roomTypeCreate'])->name('roomTypeCreate');
    Route::get('/roomTypeDelete/{id}', [App\Http\Controllers\RoomsController::class, 'roomTypeDelete'])->name('roomTypeDelete');
    Route::post('/amenityCreate', [App\Http\Controllers\RoomsController::class, 'amenityCreate'])->name('amenityCreate');
    Route::get('/amenityDelete/{id}', [App\Http\Controllers\RoomsController::class, 'amenityDelete'])->name('amenityDelete');

    Route::get('/roomCrud', [App\Http\Controllers\RoomsController::class, 'index'])->name('roomCrud');
    Route::get('/roomCrud', [App\Http\Controllers\RoomsController::class, 'index'])->name('roomCrud');
    Route::post('/saveRoom', [App\Http\Controllers\RoomsController::class, 'store'])->name('saveRoom');
    Route::get('/editRoom/{id}', [App\Http\Controllers\RoomsController::class, 'edit'])->name('editRoom');
    Route::post('/updateRoom/{id}', [App\Http\Controllers\RoomsController::class, 'update'])->name('updateRoom');
    Route::get('/destroyRoom/{id}', [App\Http\Controllers\RoomsController::class, 'destroy'])->name('destroyRoom');

    // Facilities
    Route::get('/getFacilities', [App\Http\Controllers\FacilitiesController::class, 'index'])->name('facilityCrud');
    Route::post('/saveFacility', [App\Http\Controllers\FacilitiesController::class, 'store'])->name('saveFacility');
    Route::get('/editFacility/{id}', [App\Http\Controllers\FacilitiesController::class, 'edit'])->name('editFacility');
    Route::post('/updateFacility/{id}', [App\Http\Controllers\FacilitiesController::class, 'update'])->name('updateFacility');
    Route::get('/destroyFacility/{id}', [App\Http\Controllers\FacilitiesController::class, 'destroy'])->name('destroyFacility');

    Route::get('/facilityImages/{pid}', [App\Http\Controllers\FacilitiesController::class, 'facilityImages'])->name('facilityImages');
    Route::post('/savFacilityImage/{pid}',[App\Http\Controllers\FacilitiesController::class,'savFacImage'])->name('savFacImage');
    Route::get('/destroyFacilityImage/{pid}/{id}',[App\Http\Controllers\FacilitiesController::class,'destroyFacImage'])->name('destroyFacImage');

    // Services
    Route::get('/getServices', [App\Http\Controllers\ServicesController::class, 'index'])->name('getServices');
    Route::post('/saveService', [App\Http\Controllers\ServicesController::class, 'store'])->name('saveService');
    Route::get('/editService/{id}', [App\Http\Controllers\ServicesController::class, 'edit'])->name('editService');
    Route::post('/updateService/{id}', [App\Http\Controllers\ServicesController::class, 'update'])->name('updateService');
    Route::get('/destroyService/{id}', [App\Http\Controllers\ServicesController::class, 'destroy'])->name('destroyService');

    Route::get('/serviceImages/{pid}', [App\Http\Controllers\ServicesController::class, 'serviceImages'])->name('serviceImages');
    Route::post('/savServiceImage/{pid}',[App\Http\Controllers\ServicesController::class,'savServiceImage'])->name('savServiceImage');
    Route::get('/destroyServiceImage/{pid}/{id}',[App\Http\Controllers\ServicesController::class,'destroyServiceImage'])->name('destroyServiceImage');

    // Rooms
    Route::get('/getRooms', [App\Http\Controllers\RoomsController::class, 'index'])->name('getRooms');
    Route::post('/saveRoom', [App\Http\Controllers\RoomsController::class, 'store'])->name('saveRoom');
    Route::get('/editRoom/{id}', [App\Http\Controllers\RoomsController::class, 'edit'])->name('editRoom');
    Route::post('/updateRoom/{id}', [App\Http\Controllers\RoomsController::class, 'update'])->name('updateRoom');
    Route::get('/destroyRoom/{id}', [App\Http\Controllers\RoomsController::class, 'destroy'])->name('destroyRoom');

    Route::get('/roomImages/{pid}', [App\Http\Controllers\RoomsController::class, 'roomImages'])->name('roomImages');
    Route::post('/savRoomImage/{pid}',[App\Http\Controllers\RoomsController::class,'savRoomImage'])->name('savRoomImage');
    Route::get('/destroyRoomImage/{pid}/{id}',[App\Http\Controllers\RoomsController::class,'destroyRoomImage'])->name('destroyRoomImage');
    // Partners
    Route::get('/partnerCrud', [App\Http\Controllers\PartnersController::class, 'index'])->name('partnerCrud');
    Route::post('/savePartner', [App\Http\Controllers\PartnersController::class, 'store'])->name('savePartner');
    Route::get('/editPartner/{id}', [App\Http\Controllers\PartnersController::class, 'edit'])->name('editPartner');
    Route::post('/updatePartner/{id}', [App\Http\Controllers\PartnersController::class, 'update'])->name('updatePartner');
    Route::get('/destroyPartner/{id}', [App\Http\Controllers\PartnersController::class, 'destroy'])->name('destroyPartner');

        // Gallery
        Route::get('/slides', [App\Http\Controllers\SlidesController::class, 'index'])->name('slides');
        Route::post('/saveSlide', [App\Http\Controllers\SlidesController::class, 'store'])->name('saveSlide');
        Route::get('/editSlide/{id}', [App\Http\Controllers\SlidesController::class, 'edit'])->name('editSlide');
        Route::post('/updateSlide/{id}', [App\Http\Controllers\SlidesController::class, 'update'])->name('updateSlide');
        Route::get('/destroySlide/{id}', [App\Http\Controllers\SlidesController::class, 'destroy'])->name('destroySlide');

        // Gallery
        Route::get('/getGalleries', [App\Http\Controllers\GalleryController::class, 'index'])->name('getGalleries');
        Route::post('/saveGallery', [App\Http\Controllers\GalleryController::class, 'store'])->name('saveGallery');
        Route::get('/editGallery/{id}', [App\Http\Controllers\GalleryController::class, 'edit'])->name('editGallery');
        Route::post('/updateGallery/{id}', [App\Http\Controllers\GalleryController::class, 'update'])->name('updateGallery');
        Route::get('/destroyGallery/{id}', [App\Http\Controllers\GalleryController::class, 'destroy'])->name('destroyGallery');
        
        // Bookings
        Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/search', [App\Http\Controllers\BookingController::class, 'search'])->name('searchBookings');
        Route::get('/TablesBookings', [App\Http\Controllers\BookingController::class, 'TablesBookings'])->name('TablesBookings');
        Route::get('/testBooking', [App\Http\Controllers\BookingController::class, 'create'])->name('testBooking');
        Route::post('/saveBooking', [App\Http\Controllers\BookingController::class, 'store'])->name('saveBooking');
        Route::get('/viewBooking/{id}', [App\Http\Controllers\BookingController::class, 'viewBooking'])->name('viewBooking');
        Route::get('/editBooking/{id}', [App\Http\Controllers\BookingController::class, 'edit'])->name('editBooking');
        // Route::post('/updateBooking/{id}', [App\Http\Controllers\BookingController::class, 'updateBooking'])->name('updateSlide');
        Route::get('/destroyBooking/{id}', [App\Http\Controllers\BookingController::class, 'destroy'])->name('destroyBooking');

        Route::get('/roomBookings/availableRooms/{checkinDate}', [App\Http\Controllers\BookingController::class, 'availableRooms'])->name('availableRooms');
        
        Route::get('/FoodOrders', [App\Http\Controllers\BookingController::class, 'FoodOrders'])->name('FoodOrders');
        Route::get('/Print', [App\Http\Controllers\BookingController::class, 'print'])->name('print');





});
