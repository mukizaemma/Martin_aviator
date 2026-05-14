<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\SiteAnalyticsEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class GuestBookingController extends Controller
{
    use Concerns\RendersSpaFragment;

    public function create(Request $request): View|Response
    {
        SiteAnalyticsEvent::create([
            'event_key' => 'booking_form_view',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        $rooms = Room::with('images')->orderBy('roomName')->get();
        $prefillRoomId = $request->query('room_id');
        $prefillSlug = $request->query('room');
        $selectedRoomId = null;
        if ($prefillRoomId && $rooms->contains('id', (int) $prefillRoomId)) {
            $selectedRoomId = (int) $prefillRoomId;
        } elseif ($prefillSlug) {
            $r = $rooms->firstWhere('slug', $prefillSlug);
            $selectedRoomId = $r?->id;
        }

        return $this->spaView('frontend.room-booking', compact('rooms', 'selectedRoomId'), 'Book a room');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'room_id' => 'nullable|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'additional_requests' => 'nullable|string|max:5000',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:64',
            'guest_email' => 'required|email|max:255',
            'guest_country' => 'required|string|max:120',
            'fulfillment_choice' => 'required|in:direct_pay,pay_on_delivery,booking_com,expedia',
        ])->validate();

        $setting = Setting::first();
        if ($validated['fulfillment_choice'] === 'booking_com' && empty(trim((string) ($setting->url_booking ?? '')))) {
            return back()->withErrors(['fulfillment_choice' => 'Booking.com is not configured in site settings yet.'])->withInput();
        }
        if ($validated['fulfillment_choice'] === 'expedia' && empty(trim((string) ($setting->url_expedia ?? '')))) {
            return back()->withErrors(['fulfillment_choice' => 'Expedia is not configured in site settings yet.'])->withInput();
        }

        $pickup = $request->boolean('airport_pickup');
        $dropoff = $request->boolean('airport_dropoff');

        $room = ! empty($validated['room_id']) ? Room::find($validated['room_id']) : null;

        $body = self::buildMessageBody($validated, $room, $setting, $pickup, $dropoff);

        $record = GuestBookingRequest::create([
            'room_id' => $validated['room_id'] ?? null,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'airport_pickup' => $pickup,
            'airport_dropoff' => $dropoff,
            'additional_requests' => $validated['additional_requests'] ?? null,
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
            'guest_country' => $validated['guest_country'],
            'fulfillment_choice' => $validated['fulfillment_choice'],
            'message_body' => $body,
        ]);

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_submitted',
            'properties' => ['fulfillment' => $validated['fulfillment_choice']],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        return match ($validated['fulfillment_choice']) {
            'direct_pay' => redirect()->route('pay.dpo')->with('booking_public_id', $record->public_id),
            'pay_on_delivery' => redirect()->route('room.booking.confirmation', $record->public_id),
            'booking_com' => redirect()->route('room.booking.ota', ['publicId' => $record->public_id, 'which' => 'booking_com']),
            'expedia' => redirect()->route('room.booking.ota', ['publicId' => $record->public_id, 'which' => 'expedia']),
        };
    }

    public function confirmation(string $publicId): View|Response
    {
        $booking = GuestBookingRequest::where('public_id', $publicId)->firstOrFail();
        if ($booking->fulfillment_choice !== 'pay_on_delivery') {
            abort(404);
        }

        return $this->spaView('frontend.room-booking-confirmation', compact('booking'), 'Booking received');
    }

    public function openWhatsapp(Request $request, string $publicId): RedirectResponse
    {
        $booking = GuestBookingRequest::where('public_id', $publicId)->firstOrFail();
        if ($booking->fulfillment_choice !== 'pay_on_delivery') {
            abort(404);
        }
        $digits = preg_replace('/\D+/', '', (string) (Setting::first()->phone ?? ''));
        if (strlen($digits) < 8) {
            return redirect()->route('room.booking.confirmation', $publicId)->with('error', 'WhatsApp is not configured. Please use email or call the hotel.');
        }
        $booking->update(['completed_channel' => 'whatsapp']);
        SiteAnalyticsEvent::create([
            'event_key' => 'booking_pay_delivery_whatsapp',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);
        $text = rawurlencode($booking->message_body);

        return redirect()->away('https://wa.me/'.$digits.'?text='.$text);
    }

    public function emailInstructions(Request $request, string $publicId): View|Response
    {
        $booking = GuestBookingRequest::where('public_id', $publicId)->firstOrFail();
        if ($booking->fulfillment_choice !== 'pay_on_delivery') {
            abort(404);
        }
        $email = trim((string) (Setting::first()->email ?? ''));
        if ($email === '') {
            abort(404, 'Email not configured.');
        }
        $booking->update(['completed_channel' => 'email']);
        SiteAnalyticsEvent::create([
            'event_key' => 'booking_pay_delivery_email',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);
        $subject = 'Room booking — '.(Setting::first()->company ?? 'Hotel');

        return $this->spaView('frontend.room-booking-email', compact('booking', 'email', 'subject'), 'Email booking');
    }

    public function otaRedirect(string $publicId, string $which): View|Response
    {
        abort_unless(in_array($which, ['booking_com', 'expedia'], true), 404);

        $booking = GuestBookingRequest::where('public_id', $publicId)->firstOrFail();
        $url = $which === 'expedia'
            ? (Setting::first()->url_expedia ?? '')
            : (Setting::first()->url_booking ?? '');
        if ($url === '') {
            abort(404, 'This OTA link is not configured in site settings.');
        }

        SiteAnalyticsEvent::create([
            'event_key' => $which === 'expedia' ? 'booking_ota_expedia_open' : 'booking_ota_booking_com_open',
            'properties' => [],
            'session_id' => null,
        ]);

        return $this->spaView('frontend.room-booking-ota', compact('url', 'which', 'booking'), 'Continue booking');
    }

    private static function buildMessageBody(array $v, ?Room $room, ?Setting $setting, bool $pickup, bool $dropoff): string
    {
        $hotel = $setting->company ?? 'Hotel';
        $lines = [];
        $lines[] = '*'.$hotel.' — room booking request*';
        $lines[] = '';
        if ($room) {
            $lines[] = 'Room: '.$room->roomName;
        } else {
            $lines[] = 'Room: (not specified — please advise availability)';
        }
        $lines[] = 'Check-in: '.$v['check_in'];
        $lines[] = 'Check-out: '.$v['check_out'];
        $lines[] = 'Airport pickup: '.($pickup ? 'Yes' : 'No');
        $lines[] = 'Airport drop-off: '.($dropoff ? 'Yes' : 'No');
        if (! empty($v['additional_requests'])) {
            $lines[] = 'Additional requests: '.$v['additional_requests'];
        }
        $lines[] = '';
        $lines[] = 'Guest';
        $lines[] = 'Name: '.$v['guest_name'];
        $lines[] = 'Phone: '.$v['guest_phone'];
        $lines[] = 'Email: '.$v['guest_email'];
        $lines[] = 'Country: '.$v['guest_country'];
        $lines[] = '';
        $lines[] = 'Fulfillment choice: '.match ($v['fulfillment_choice']) {
            'direct_pay' => 'Direct pay (card — DPO on website)',
            'pay_on_delivery' => 'Pay on arrival (confirm via WhatsApp or email)',
            'booking_com' => 'Book on Booking.com',
            'expedia' => 'Book on Expedia',
            default => $v['fulfillment_choice'],
        };
        $lines[] = '— Sent from the hotel website booking form.';

        return implode("\n", $lines);
    }
}
