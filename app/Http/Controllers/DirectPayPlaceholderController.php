<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\SiteAnalyticsEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DirectPayPlaceholderController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        SiteAnalyticsEvent::create([
            'event_key' => 'direct_pay_page_view',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        $params = [];
        if ($request->filled('room')) {
            $room = Room::where('slug', $request->query('room'))->first();
            if ($room) {
                $request->session()->put('booking_room_slug', $room->slug);
                $params['room'] = $room->slug;
            }
        }

        return redirect()->route('room.booking', $params);
    }
}
