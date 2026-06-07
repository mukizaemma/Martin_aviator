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

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @php
                        $activeFilters = array_filter($filters);
                    @endphp

                    <div class="row g-3 mb-4">
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('guestReservations', request()->except(['confirmation_channel', 'payment_timing', 'page'])) }}"
                               class="text-decoration-none text-reset">
                                <div class="card border-0 shadow-sm h-100 {{ empty($activeFilters) ? 'border border-primary' : '' }}">
                                    <div class="card-body py-3">
                                        <div class="small text-muted">Total</div>
                                        <div class="h4 mb-0">{{ $stats['total'] }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('guestReservations', array_merge(request()->except('page'), ['payment_timing' => 'pay_direct'])) }}"
                               class="text-decoration-none text-reset">
                                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning {{ ($filters['payment_timing'] ?? null) === 'pay_direct' ? 'border border-warning' : '' }}">
                                    <div class="card-body py-3">
                                        <div class="small text-muted">Pay directly</div>
                                        <div class="h4 mb-0">{{ $stats['pay_direct'] }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('guestReservations', array_merge(request()->except('page'), ['payment_timing' => 'pay_at_hotel'])) }}"
                               class="text-decoration-none text-reset">
                                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success {{ ($filters['payment_timing'] ?? null) === 'pay_at_hotel' ? 'border border-success' : '' }}">
                                    <div class="card-body py-3">
                                        <div class="small text-muted">Pay at hotel</div>
                                        <div class="h4 mb-0">{{ $stats['pay_at_hotel'] }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('guestReservations', array_merge(request()->except('page'), ['confirmation_channel' => 'whatsapp'])) }}"
                               class="text-decoration-none text-reset">
                                <div class="card border-0 shadow-sm h-100 {{ ($filters['confirmation_channel'] ?? null) === 'whatsapp' ? 'border border-success' : '' }}">
                                    <div class="card-body py-3">
                                        <div class="small text-muted">Via WhatsApp</div>
                                        <div class="h4 mb-0">{{ $stats['via_whatsapp'] }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('guestReservations', array_merge(request()->except('page'), ['confirmation_channel' => 'email'])) }}"
                               class="text-decoration-none text-reset">
                                <div class="card border-0 shadow-sm h-100 {{ ($filters['confirmation_channel'] ?? null) === 'email' ? 'border border-info' : '' }}">
                                    <div class="card-body py-3">
                                        <div class="small text-muted">Via email</div>
                                        <div class="h4 mb-0">{{ $stats['via_email'] }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <a href="{{ route('guestReservations', array_merge(request()->except('page'), ['confirmation_channel' => 'card'])) }}"
                               class="text-decoration-none text-reset">
                                <div class="card border-0 shadow-sm h-100 {{ ($filters['confirmation_channel'] ?? null) === 'card' ? 'border border-secondary' : '' }}">
                                    <div class="card-body py-3">
                                        <div class="small text-muted">Via card</div>
                                        <div class="h4 mb-0">{{ $stats['via_card'] }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <strong>Online booking requests</strong>
                                    <span class="text-muted small ms-2">From the booking wizard</span>
                                </div>
                                <a href="{{ route('guestReservations.print', $filters) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   target="_blank"
                                   rel="noopener">
                                    <i class="fas fa-print"></i> Print list
                                </a>
                            </div>
                        </div>
                        <div class="card-body border-bottom">
                            <form action="{{ route('guestReservations') }}" method="GET" class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label for="confirmation_channel" class="form-label small mb-1">Confirmed via</label>
                                    <select name="confirmation_channel" id="confirmation_channel" class="form-select form-select-sm">
                                        <option value="">All channels</option>
                                        <option value="whatsapp" @selected(($filters['confirmation_channel'] ?? '') === 'whatsapp')>WhatsApp</option>
                                        <option value="email" @selected(($filters['confirmation_channel'] ?? '') === 'email')>Email</option>
                                        <option value="card" @selected(($filters['confirmation_channel'] ?? '') === 'card')>Card</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_timing" class="form-label small mb-1">Payment</label>
                                    <select name="payment_timing" id="payment_timing" class="form-select form-select-sm">
                                        <option value="">All payments</option>
                                        <option value="pay_direct" @selected(($filters['payment_timing'] ?? '') === 'pay_direct')>Pay directly</option>
                                        <option value="pay_at_hotel" @selected(($filters['payment_timing'] ?? '') === 'pay_at_hotel')>Pay at hotel</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label small mb-1">Status</label>
                                    <select name="status" id="status" class="form-select form-select-sm">
                                        <option value="">All statuses</option>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label small mb-1">From date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                                           value="{{ $filters['start_date'] ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label small mb-1">To date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                                           value="{{ $filters['end_date'] ?? '' }}">
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                    <a href="{{ route('guestReservations') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                                </div>
                            </form>
                            @if (! empty($activeFilters))
                                <div class="small text-muted mt-2">
                                    Showing filtered results
                                    @if ($filters['confirmation_channel'])
                                        · Confirmed via {{ ucfirst($filters['confirmation_channel']) }}
                                    @endif
                                    @if ($filters['payment_timing'])
                                        · {{ $filters['payment_timing'] === 'pay_direct' ? 'Pay directly' : 'Pay at hotel' }}
                                    @endif
                                    @if ($filters['status'])
                                        · Status: {{ $statuses[$filters['status']] ?? $filters['status'] }}
                                    @endif
                                    @if ($filters['start_date'] || $filters['end_date'])
                                        · {{ $filters['start_date'] ?: '…' }} to {{ $filters['end_date'] ?: '…' }}
                                    @endif
                                </div>
                            @endif
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
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reservations as $r)
                                        @php
                                            $phoneDigits = preg_replace('/[^\d+]/', '', $r->phone);
                                            $statusClass = match ($r->status) {
                                                'arrived' => 'bg-success',
                                                'no_show' => 'bg-warning text-dark',
                                                'canceled' => 'bg-danger',
                                                'duplicated' => 'bg-secondary',
                                                default => 'bg-light text-dark',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="small">{{ $r->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <strong>{{ $r->names }}</strong><br>
                                                @if ($r->email)
                                                    <a href="mailto:{{ $r->email }}" class="small">{{ $r->email }}</a>
                                                @endif
                                                @if ($r->email && $r->phone)
                                                    <span class="small text-muted"> · </span>
                                                @endif
                                                @if ($r->phone)
                                                    <a href="tel:{{ $phoneDigits }}" class="small">{{ $r->phone }}</a>
                                                @endif
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
                                            <td>
                                                <form action="{{ route('guestReservations.updateStatus', $r) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-select form-select-sm {{ $statusClass }}" style="min-width: 140px;" onchange="this.form.submit()">
                                                        @foreach ($statuses as $value => $label)
                                                            <option value="{{ $value }}" @selected($r->status === $value)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('guestReservations.destroy', $r) }}" method="POST" class="d-inline js-remove-reservation">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No website reservations match your filters.</td>
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

@section('scripts')
<script>
    document.querySelectorAll('.js-remove-reservation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!confirm('Remove this reservation from the list?')) {
                event.preventDefault();
                return;
            }
            if (!confirm('This action cannot be undone. Are you sure you want to permanently remove it?')) {
                event.preventDefault();
            }
        });
    });
</script>
@endsection
