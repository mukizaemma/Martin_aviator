<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\View\View;

class GuestReservationController extends Controller
{
    public function index(): View
    {
        $reservations = Reservation::with('room')->latest()->paginate(25);

        $stats = [
            'total' => Reservation::count(),
            'pay_direct' => Reservation::where('payment_timing', 'pay_direct')->count(),
            'pay_at_hotel' => Reservation::where('payment_timing', 'pay_at_hotel')->count(),
            'via_whatsapp' => Reservation::where('confirmation_channel', 'whatsapp')->count(),
            'via_email' => Reservation::where('confirmation_channel', 'email')->count(),
            'via_card' => Reservation::where('confirmation_channel', 'card')->count(),
        ];

        return view('admin.guest-reservations', compact('reservations', 'stats'));
    }
}
