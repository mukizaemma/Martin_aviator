<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Website reservations — print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            color: #111;
        }

        h1 {
            font-size: 1.35rem;
            margin: 0 0 6px;
        }

        .meta {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f3f3;
        }

        .toolbar {
            margin-bottom: 16px;
        }

        @media print {
            .toolbar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <h1>Online booking requests</h1>
    <div class="meta">
        Printed {{ now()->format('d M Y H:i') }}
        @if ($filters['start_date'] || $filters['end_date'])
            · Date range:
            {{ $filters['start_date'] ?: '…' }}
            to
            {{ $filters['end_date'] ?: '…' }}
        @endif
        @if ($filters['confirmation_channel'])
            · Confirmed via: {{ ucfirst($filters['confirmation_channel']) }}
        @endif
        @if ($filters['payment_timing'])
            · Payment: {{ $filters['payment_timing'] === 'pay_direct' ? 'Pay directly' : 'Pay at hotel' }}
        @endif
        @if ($filters['status'])
            · Status: {{ $statuses[$filters['status']] ?? $filters['status'] }}
        @endif
        · {{ $reservations->count() }} record(s)
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Guest</th>
                <th>Contact</th>
                <th>Stay</th>
                <th>Room</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Confirmed via</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $r)
                <tr>
                    <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $r->names }}</td>
                    <td>
                        {{ $r->email }}<br>
                        {{ $r->phone }}
                    </td>
                    <td>
                        {{ $r->checkin->format('d M Y') }} → {{ $r->checkout->format('d M Y') }}<br>
                        {{ $r->nights }} night(s), {{ $r->adults }} adult(s)
                    </td>
                    <td>{{ $r->room?->roomName ?? '—' }}</td>
                    <td>${{ number_format((float) $r->total, 2) }}</td>
                    <td>
                        @if ($r->payment_timing === 'pay_direct')
                            Pay directly
                        @elseif ($r->payment_timing === 'pay_at_hotel')
                            Pay at hotel
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $r->confirmation_channel ? ucfirst($r->confirmation_channel) : '—' }}</td>
                    <td>{{ $statuses[$r->status] ?? ucfirst($r->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No reservations match the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
