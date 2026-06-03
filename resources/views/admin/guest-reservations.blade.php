@extends('layouts.adminbase')

@section('sidebar')
    @parent
@endsection

@section('content')
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            @include('admin.includes.sidenav')
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ route('bookings') }}">Legacy bookings</a></li>
                        <li class="breadcrumb-item active">Website reservations</li>
                    </ol>

                    <div class="row g-3 mb-4">
                        <div class="col-md-2 col-sm-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3">
                                    <div class="small text-muted">Total</div>
                                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                                <div class="card-body py-3">
                                    <div class="small text-muted">Pay directly</div>
                                    <div class="h4 mb-0">{{ $stats['pay_direct'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                                <div class="card-body py-3">
                                    <div class="small text-muted">Pay at hotel</div>
                                    <div class="h4 mb-0">{{ $stats['pay_at_hotel'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3">
                                    <div class="small text-muted">Via WhatsApp</div>
                                    <div class="h4 mb-0">{{ $stats['via_whatsapp'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3">
                                    <div class="small text-muted">Via email</div>
                                    <div class="h4 mb-0">{{ $stats['via_email'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body py-3">
                                    <div class="small text-muted">Via card</div>
                                    <div class="h4 mb-0">{{ $stats['via_card'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>Online booking requests</strong>
                            <span class="text-muted small ms-2">From the booking wizard</span>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Guest</th>
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
                                            <td class="small">{{ $r->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <strong>{{ $r->names }}</strong><br>
                                                <span class="small text-muted">{{ $r->email }} · {{ $r->phone }}</span>
                                            </td>
                                            <td class="small">
                                                {{ $r->checkin->format('d M Y') }} → {{ $r->checkout->format('d M Y') }}<br>
                                                {{ $r->nights }} night(s), {{ $r->adults }} adult(s)
                                            </td>
                                            <td>{{ $r->room?->roomName ?? '—' }}</td>
                                            <td>${{ number_format((float) $r->total, 2) }}</td>
                                            <td>
                                                @if ($r->payment_timing === 'pay_direct')
                                                    <span class="badge bg-warning text-dark">Pay directly</span>
                                                @elseif ($r->payment_timing === 'pay_at_hotel')
                                                    <span class="badge bg-success">Pay at hotel</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($r->confirmation_channel === 'whatsapp')
                                                    <span class="badge bg-success">WhatsApp</span>
                                                @elseif ($r->confirmation_channel === 'email')
                                                    <span class="badge bg-info text-dark">Email</span>
                                                @elseif ($r->confirmation_channel === 'card')
                                                    <span class="badge bg-secondary">Card</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-light text-dark">{{ $r->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No website reservations yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $reservations->links() }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
