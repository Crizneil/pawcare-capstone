@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4 fade-in">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Appointments</h2>
            <p class="text-muted">Manage walk-ins and scheduled vaccinations.</p>
        </div>
        <button class="btn btn-orange rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#walkInModal">
            <i data-lucide="plus" class="me-2"></i> New Walk-in
        </button>
    </div>

    {{-- Sub-menu Navigation --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link rounded-pill {{ $view == 'today' ? 'active bg-primary' : 'text-muted' }}"
                       href="{{ route('staff.appointments', ['view' => 'today']) }}">
                        <i data-lucide="calendar" class="me-2" style="width: 16px;"></i> Today
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill {{ $view == 'upcoming' ? 'active bg-primary' : 'text-muted' }}"
                       href="{{ route('staff.appointments', ['view' => 'upcoming']) }}">
                        <i data-lucide="clock" class="me-2" style="width: 16px;"></i> Upcoming
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-pill {{ $view == 'completed' ? 'active bg-primary' : 'text-muted' }}"
                       href="{{ route('staff.appointments', ['view' => 'completed']) }}">
                        <i data-lucide="check-circle" class="me-2" style="width: 16px;"></i> Completed
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Appointment List --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Pet Details</th>
                        <th>Owner</th>
                        <th>Service</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $apt)
                    <tr>
                        {{-- 1. Pet Details --}}
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $apt->pet_name }}</div>
                            <small class="text-muted text-uppercase">{{ $apt->species }}</small>
                        </td>

                        {{-- 2. Owner --}}
                        <td>
                            @if($apt->user_id)
                                <div class="fw-bold text-dark">{{ $apt->user->name }}</div>
                                <div class="small text-muted">Registered</div>
                            @else
                                <div class="fw-bold text-muted italic">Guest</div>
                            @endif
                        </td>

                        {{-- 3. Service (This was missing/shifted) --}}
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3">
                                {{ $apt->service_type ?? 'General' }}
                            </span>
                        </td>

                        {{-- 4. Schedule --}}
                        <td>
                            <div class="small fw-bold">{{ date('M d, Y', strtotime($apt->appointment_date)) }}</div>
                            <div class="text-muted small">{{ date('h:i A', strtotime($apt->appointment_time)) }}</div>
                        </td>

                        {{-- 5. Status --}}
                        <td>
                            @if($apt->status == 'pending')
                                <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning">Pending</span>
                            @elseif($apt->status == 'approved')
                                <span class="badge rounded-pill bg-info-subtle text-info border border-info">Approved</span>
                            @elseif($apt->status == 'Done')
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success">Completed</span>
                            @endif
                        </td>

                        {{-- 6. Actions --}}
                        <td class="text-end pe-4">
                            @if($apt->status == 'pending' || $apt->status == 'approved')
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                    <form action="{{ route('staff.appointments.update', [$apt->id, 'Done']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border-end px-3 text-success fw-bold">
                                            <i data-lucide="check" style="width: 14px;"></i> Done
                                        </button>
                                    </form>
                                    <form action="{{ route('staff.appointments.update', [$apt->id, 'Missed']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light px-3 text-danger fw-bold">
                                            <i data-lucide="x" style="width: 14px;"></i> Missed
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small"><i data-lucide="lock" style="width: 12px;"></i> Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appointments->hasPages())
        <div class="card-footer bg-white border-0 p-3">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>
@include('partials._walk-in_modal')
@endsection
