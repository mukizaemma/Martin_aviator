<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestReservationController extends Controller
{
    public const STATUSES = [
        'pending' => 'Pending',
        'arrived' => 'Arrived',
        'no_show' => 'No show',
        'canceled' => 'Canceled',
        'duplicated' => 'Duplicated',
    ];

    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $reservations = $this->filteredQuery($filters)
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => Reservation::count(),
            'pay_direct' => Reservation::where('payment_timing', 'pay_direct')->count(),
            'pay_at_hotel' => Reservation::where('payment_timing', 'pay_at_hotel')->count(),
            'via_whatsapp' => Reservation::where('confirmation_channel', 'whatsapp')->count(),
            'via_email' => Reservation::where('confirmation_channel', 'email')->count(),
            'via_card' => Reservation::where('confirmation_channel', 'card')->count(),
        ];

        return view('admin.guest-reservations', [
            'reservations' => $reservations,
            'stats' => $stats,
            'filters' => $filters,
            'statuses' => self::STATUSES,
        ]);
    }

    public function print(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $reservations = $this->filteredQuery($filters)->get();

        return view('admin.guest-reservations-print', [
            'reservations' => $reservations,
            'filters' => $filters,
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(self::STATUSES)),
        ]);

        $reservation->update(['status' => $validated['status']]);

        return redirect()
            ->back()
            ->with('success', 'Reservation status updated.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();

        return redirect()
            ->back()
            ->with('success', 'Reservation removed.');
    }

    /**
     * @return array{confirmation_channel: ?string, payment_timing: ?string, status: ?string, start_date: ?string, end_date: ?string}
     */
    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'confirmation_channel' => 'nullable|in:whatsapp,email,card',
            'payment_timing' => 'nullable|in:pay_direct,pay_at_hotel',
            'status' => 'nullable|in:'.implode(',', array_keys(self::STATUSES)),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        if ($startDate && $endDate && $endDate < $startDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [
            'confirmation_channel' => $validated['confirmation_channel'] ?? null,
            'payment_timing' => $validated['payment_timing'] ?? null,
            'status' => $validated['status'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * @param  array{confirmation_channel: ?string, payment_timing: ?string, status: ?string, start_date: ?string, end_date: ?string}  $filters
     */
    private function filteredQuery(array $filters)
    {
        $query = Reservation::with('room')->latest();

        if ($filters['confirmation_channel']) {
            $query->where('confirmation_channel', $filters['confirmation_channel']);
        }

        if ($filters['payment_timing']) {
            $query->where('payment_timing', $filters['payment_timing']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['start_date']) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if ($filters['end_date']) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query;
    }
}
