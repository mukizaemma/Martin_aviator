<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
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

        $setting = Setting::first();
        $rooms = Room::with('images')->orderBy('roomName')->get();
        $prefillRoomId = $request->query('room_id');
        $prefillSlug = $request->query('room') ?: $request->session()->get('booking_room_slug');
        $selectedRoomId = null;
        if ($prefillRoomId && $rooms->contains('id', (int) $prefillRoomId)) {
            $selectedRoomId = (int) $prefillRoomId;
            $match = $rooms->firstWhere('id', (int) $prefillRoomId);
            if ($match?->slug) {
                $request->session()->put('booking_room_slug', $match->slug);
            }
        } elseif ($prefillSlug) {
            $r = $rooms->firstWhere('slug', $prefillSlug);
            $selectedRoomId = $r?->id;
            if ($selectedRoomId) {
                $request->session()->put('booking_room_slug', $prefillSlug);
            }
        }

        $waDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?? ''));
        $hotelEmail = trim((string) ($setting->email ?? ''));
        $whatsappAvailable = strlen($waDigits) >= 8;
        $emailAvailable = $hotelEmail !== '';
        $bookingChannelsReady = $whatsappAvailable && $emailAvailable;
        $directPayEnabled = (bool) config('booking.direct_pay_enabled');

        $roomsPayload = $rooms->map(fn (Room $r) => [
            'id' => $r->id,
            'slug' => $r->slug,
            'name' => $r->roomName,
            'price' => (float) $r->price,
            'priceLabel' => \App\Support\Currency::formatRoomPriceLabel($r->price, $r->price_rwf),
            'maxAdults' => (int) $r->maxAdults,
            'maxChildren' => (int) $r->maxChildren,
        ])->values();

        return $this->spaView('frontend.room-booking', compact(
            'rooms',
            'selectedRoomId',
            'roomsPayload',
            'bookingChannelsReady',
            'whatsappAvailable',
            'emailAvailable',
            'directPayEnabled',
        ), 'Book your stay');
    }

    public function store(Request $request): RedirectResponse
    {
        $setting = Setting::first();
        $waDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?? ''));
        $hotelEmail = trim((string) ($setting->email ?? ''));
        if (strlen($waDigits) < 8 || $hotelEmail === '') {
            return back()->withErrors([
                'booking' => 'Online booking is temporarily unavailable. Please call the hotel or try again later.',
            ])->withInput();
        }

        $validated = Validator::make($request->all(), [
            'line_items' => 'required|json',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'room_count' => 'required|integer|min:1|max:10',
            'additional_requests' => 'nullable|string|max:5000',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:64',
            'guest_email' => 'required|email|max:255',
            'guest_country' => 'required|string|max:120',
            'payment_timing' => 'required|in:pay_direct,pay_at_hotel',
            'confirmation_channel' => 'required|in:card,whatsapp,email',
        ])->validate();

        $directPayEnabled = (bool) config('booking.direct_pay_enabled');

        if ($validated['payment_timing'] === 'pay_direct') {
            if ($validated['confirmation_channel'] !== 'card') {
                return back()->withErrors(['confirmation_channel' => 'Invalid payment confirmation.'])->withInput();
            }
            if (! $directPayEnabled) {
                return back()->withErrors([
                    'payment_timing' => 'Card payment is not available yet. Please book and pay at the hotel.',
                ])->withInput();
            }
            Validator::make($request->all(), [
                'card_name' => 'required|string|max:120',
                'card_number' => 'required|string|min:13|max:24',
                'card_exp' => 'required|string|max:7',
                'card_cvc' => 'required|string|min:3|max:4',
            ])->validate();
        } else {
            if (! in_array($validated['confirmation_channel'], ['whatsapp', 'email'], true)) {
                return back()->withErrors(['confirmation_channel' => 'Please choose WhatsApp or email.'])->withInput();
            }
            if ($validated['confirmation_channel'] === 'whatsapp' && strlen($waDigits) < 8) {
                return back()->withErrors(['confirmation_channel' => 'WhatsApp is not configured.'])->withInput();
            }
            if ($validated['confirmation_channel'] === 'email' && $hotelEmail === '') {
                return back()->withErrors(['confirmation_channel' => 'Hotel email is not configured.'])->withInput();
            }
        }

        $lineItems = json_decode($validated['line_items'], true);
        if (! is_array($lineItems) || count($lineItems) < 1) {
            return back()->withErrors(['line_items' => 'Please add at least one room to your stay.'])->withInput();
        }

        $pickup = $request->boolean('airport_pickup');
        $dropoff = $request->boolean('airport_dropoff');

        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];
        $nights = max(1, (int) now()->parse($checkIn)->diffInDays($checkOut));

        $total = 0.0;
        $primaryRoomId = null;
        $normalizedLines = [];
        foreach ($lineItems as $line) {
            $roomId = isset($line['room_id']) ? (int) $line['room_id'] : 0;
            $room = $roomId > 0 ? Room::find($roomId) : null;
            if ($room && $primaryRoomId === null) {
                $primaryRoomId = $room->id;
            }
            $lineNights = isset($line['nights']) ? max(1, (int) $line['nights']) : $nights;
            $lineTotal = $room ? (float) $room->price * $lineNights : 0;
            $total += $lineTotal;
            $normalizedLines[] = [
                'room_id' => $room?->id,
                'room_name' => $room?->roomName ?? ($line['room_name'] ?? 'Room'),
                'check_in' => $line['check_in'] ?? $checkIn,
                'check_out' => $line['check_out'] ?? $checkOut,
                'nights' => $lineNights,
                'line_total' => $lineTotal,
            ];
        }

        $bookingOption = $validated['payment_timing'].'_'.$validated['confirmation_channel'];

        $body = self::buildMessageBody(
            $validated,
            $normalizedLines,
            $setting,
            $pickup,
            $dropoff,
            $nights,
            $total
        );

        $record = Reservation::create([
            'room_id' => $primaryRoomId,
            'line_items' => $normalizedLines,
            'checkin' => $checkIn,
            'checkout' => $checkOut,
            'adults' => (int) $validated['adults'],
            'children' => (int) ($validated['children'] ?? 0),
            'rooms' => (int) $validated['room_count'],
            'nights' => $nights,
            'total' => $total,
            'names' => $validated['guest_name'],
            'phone' => $validated['guest_phone'],
            'email' => $validated['guest_email'],
            'address' => $validated['guest_country'],
            'status' => 'pending',
            'description' => $validated['additional_requests'] ?? null,
            'booking_option' => $bookingOption,
            'payment_timing' => $validated['payment_timing'],
            'confirmation_channel' => $validated['confirmation_channel'],
            'airport_pickup' => $pickup,
            'airport_dropoff' => $dropoff,
            'message_body' => $body,
        ]);

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_submitted',
            'properties' => [
                'payment_timing' => $validated['payment_timing'],
                'confirmation_channel' => $validated['confirmation_channel'],
            ],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        return match ($validated['confirmation_channel']) {
            'whatsapp' => redirect()->route('room.booking.whatsapp', $record->public_id),
            'email' => redirect()->route('room.booking.email', $record->public_id),
            default => redirect()->route('room.booking.confirmation', $record->public_id),
        };
    }

    public function confirmation(string $publicId): View|Response
    {
        $booking = Reservation::with('room')->where('public_id', $publicId)->firstOrFail();
        $setting = Setting::first();

        return $this->spaView('frontend.room-booking-confirmation', compact('booking', 'setting'), 'Booking received');
    }

    public function openWhatsapp(Request $request, string $publicId): RedirectResponse
    {
        $booking = Reservation::with('room')->where('public_id', $publicId)->firstOrFail();
        if ($booking->confirmation_channel !== 'whatsapp') {
            abort(404);
        }

        $digits = preg_replace('/\D+/', '', (string) (Setting::first()->phone ?? ''));
        if (strlen($digits) < 8) {
            return redirect()->route('room.booking.confirmation', $publicId)
                ->with('error', 'WhatsApp is not configured. Please email the hotel instead.');
        }

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_whatsapp_open',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        return redirect()->away('https://wa.me/'.$digits.'?text='.rawurlencode($booking->message_body));
    }

    public function emailInstructions(Request $request, string $publicId): View|Response
    {
        $booking = Reservation::with('room')->where('public_id', $publicId)->firstOrFail();
        if ($booking->confirmation_channel !== 'email') {
            abort(404);
        }

        $email = trim((string) (Setting::first()->email ?? ''));
        if ($email === '') {
            abort(404, 'Email not configured.');
        }

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_email_open',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        $subject = 'Room booking — '.(Setting::first()->company ?? 'Hotel');

        return $this->spaView('frontend.room-booking-email', compact('booking', 'email', 'subject'), 'Email booking');
    }

    public function otaRedirect(string $publicId, string $which): View|Response
    {
        abort(404);
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    private static function buildMessageBody(
        array $v,
        array $lines,
        ?Setting $setting,
        bool $pickup,
        bool $dropoff,
        int $nights,
        float $total
    ): string {
        $hotel = $setting->company ?? 'Hotel';
        $linesOut = [];
        $linesOut[] = '*'.$hotel.' — room booking request*';
        $linesOut[] = '';
        $linesOut[] = 'Stay: '.$v['check_in'].' → '.$v['check_out'].' ('.$nights.' night(s))';
        $linesOut[] = 'Guests: '.$v['adults'].' adult(s), '.((int) ($v['children'] ?? 0)).' child(ren), '.$v['room_count'].' room(s)';
        $linesOut[] = '';
        $linesOut[] = '*Rooms*';
        foreach ($lines as $line) {
            $linesOut[] = '• '.($line['room_name'] ?? 'Room')
                .' — '.($line['check_in'] ?? '').' → '.($line['check_out'] ?? '')
                .($line['line_total'] > 0 ? ' — $'.number_format((float) $line['line_total'], 2) : ' — rate on request');
        }
        if ($total > 0) {
            $linesOut[] = '';
            $linesOut[] = 'Estimated total: $'.number_format($total, 2);
        }
        $linesOut[] = '';
        $linesOut[] = 'Airport pickup: '.($pickup ? 'Yes' : 'No');
        $linesOut[] = 'Airport drop-off: '.($dropoff ? 'Yes' : 'No');
        if (! empty($v['additional_requests'])) {
            $linesOut[] = 'Special request: '.$v['additional_requests'];
        }
        $linesOut[] = '';
        $linesOut[] = '*Guest*';
        $linesOut[] = 'Name: '.$v['guest_name'];
        $linesOut[] = 'Phone: '.$v['guest_phone'];
        $linesOut[] = 'Email: '.$v['guest_email'];
        $linesOut[] = 'Country: '.$v['guest_country'];
        $linesOut[] = '';
        $linesOut[] = 'Payment: '.match ($v['payment_timing']) {
            'pay_direct' => 'Book and pay directly (card)',
            'pay_at_hotel' => 'Book and pay at the hotel',
            default => $v['payment_timing'],
        };
        $linesOut[] = 'Confirm via: '.match ($v['confirmation_channel']) {
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            'card' => 'Card',
            default => $v['confirmation_channel'],
        };
        $linesOut[] = '— Sent from the hotel website booking form.';

        return implode("\n", $linesOut);
    }
}
