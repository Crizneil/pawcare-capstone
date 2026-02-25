@extends('layouts.admin')

@section('page_title', 'Appointments Dashboard')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Appointment Management</h2>
            <p class="text-muted small">Schedule and manage pet healthcare visits.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 p-3" style="background: linear-gradient(135deg, #fff 60%, #fffbf7 100%);">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-orange-subtle p-3 me-3">
                    <i class="bi bi-calendar-check text-orange fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Today</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $counts['today'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning-subtle p-3 me-3">
                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Pending</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $counts['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle p-3 me-3">
                    <i class="bi bi-check2-circle text-primary fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Approved</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $counts['approved'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-0 h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success-subtle p-3 me-3">
                    <i class="bi bi-flag-fill text-success fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Completed</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $counts['completed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- FILTER SECTION -->
    <div class="card shadow-sm border-0 mb-4 p-3">
        <form method="GET" action="{{ route('admin.appointments') }}">
            <div class="row g-2 align-items-end">

                <div class="col-md-2">
                    <label class="small text-muted">Status</label>
                    <select name="status" class="form-control rounded-pill">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small text-muted">From</label>
                    <input type="date" name="from" class="form-control rounded-pill">
                </div>

                <div class="col-md-2">
                    <label class="small text-muted">To</label>
                    <input type="date" name="to" class="form-control rounded-pill">
                </div>

                <div class="col-md-2">
                    <label class="small text-muted">Owner</label>
                    <input type="text" name="owner" class="form-control rounded-pill">
                </div>

                <div class="col-md-2">
                    <label class="small text-muted">Pet</label>
                    <input type="text" name="pet" class="form-control rounded-pill">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-orange w-100 rounded-pill">
                        Filter
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- APPOINTMENT TABLE -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Appointment ID</th>
                        <th>Date & Time</th>
                        <th>Owner</th>
                        <th>Pet</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($appointments as $appointment)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">#APT-{{ $appointment->id }}</div>
                            <small class="text-primary" style="font-size: 0.75rem; letter-spacing: 0.5px;">SYSTEM GEN</small>
                        </td>

                        <td>
                            <div class="fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                            </div>
                            <div class="small text-muted">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                            </div>
                        </td>

                        <td>
                            <div class="fw-bold text-dark">{{ $appointment->user->name }}</div>
                            <div class="small text-muted">{{ $appointment->user->phone ?? 'No Contact' }}</div>
                        </td>

                        <td>
                            <div class="fw-bold text-dark">{{ $appointment->pet_name }}</div>
                            <div class="small text-muted">{{ ucfirst($appointment->species) }}</div>
                        </td>

                        <td>
                            <div class="fw-bold text-dark">{{ ucfirst($appointment->service_type) }}</div>
                            <div class="small text-muted">Veterinary Care</div>
                        </td>

                        <td>
                            @php
                                $statusClasses = [
                                    'pending'   => 'bg-warning-subtle text-dark border-warning-subtle',
                                    'approved'  => 'bg-primary-subtle text-dark border-primary-subtle',
                                    'completed' => 'bg-success-subtle text-dark border-success-subtle',
                                    'Done'      => 'bg-success-subtle text-dark border-success-subtle',
                                    'cancelled' => 'bg-danger-subtle text-dark border-danger-subtle',
                                    'rejected'  => 'bg-secondary-subtle text-dark border-secondary-subtle',
                                ];
                                $class = $statusClasses[$appointment->status] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge rounded-pill border fw-bold px-3 py-2 {{ $class }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>

                        <td class="text-end pe-4 action-cell">
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm btn-light border rounded-pill px-3 fw-medium shadow-sm"
                                        type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    Manage <i class="bi bi-three-dots-vertical ms-1"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                    {{-- SHOW THESE IF STATUS IS PENDING --}}
                                    @if($appointment->status == 'pending')
                                        <li>
                                            <form action="{{ route('admin.appointments.approve', $appointment->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-2 text-success fw-bold">
                                                    <i class="bi bi-check2-circle me-2"></i> Approve
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            {{-- UPDATED: Reject Trigger --}}
                                            <button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $appointment->id }}">
                                                <i class="bi bi-x-circle me-2"></i> Reject
                                            </button>
                                        </li>
                                    @endif

                                    {{-- SHOW THESE IF PENDING OR APPROVED --}}
                                    @if(in_array($appointment->status, ['pending', 'approved']))
                                        <li>
                                            {{-- UPDATED: Reschedule Trigger --}}
                                            <button type="button" class="dropdown-item py-2 text-primary" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $appointment->id }}">
                                                <i class="bi bi-calendar-range me-2"></i> Reschedule
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            {{-- UPDATED: Cancel Trigger --}}
                                            <button type="button" class="dropdown-item py-2 text-muted small" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $appointment->id }}">
                                                <i class="bi bi-slash-circle me-2"></i> Cancel
                                            </button>
                                        </li>
                                    @endif

                                    {{-- IF COMPLETED - NO ACTIONS NEEDED --}}
                                    @if(in_array($appointment->status, ['completed', 'Done']))
                                        <li><span class="dropdown-item-text small text-muted">No actions available</span></li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        No appointments found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $appointments->links() }}
    </div>
</div>
    @foreach($appointments as $appointment)
        @include('partials._reject_modal')
        @include('partials._reschedule_modal')
        @include('partials._cancel_modal')
    @endforeach
@endsection
