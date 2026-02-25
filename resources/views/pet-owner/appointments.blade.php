@extends('layouts.admin')

@section('page_title', 'Appointments Dashboard')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manage Appointments</h2>
            <p class="text-muted small">Manage your pet's vaccination appointments and track status.</p>
        </div>
        <button class="btn btn-orange rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
            <i class="bi bi-plus-lg me-2"></i> Book Appointment
        </button>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary text-uppercase small fw-bold">
                            <th class="ps-4">Date & Time</th>
                            <th>Pet</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $appointment->pet_name }}</div>
                                <div class="small text-muted">{{ ucfirst($appointment->species) }}</div>
                            </td>
                            <td>
                                <div class="badge bg-info-subtle text-info border border-info px-3 rounded-pill">
                                    {{ ucfirst($appointment->service_type) }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusStyles = [
                                        'pending'   => 'bg-warning-subtle text-warning border-warning',
                                        'approved'  => 'bg-primary-subtle text-primary border-primary',
                                        'completed' => 'bg-success-subtle text-success border-success',
                                        'done'      => 'bg-success-subtle text-success border-success',
                                        'missed'    => 'bg-danger-subtle text-danger border-danger',
                                        'cancelled' => 'bg-secondary-subtle text-secondary border-secondary',
                                        'rejected'  => 'bg-danger-subtle text-danger border-danger',
                                    ];
                                    $currentStyle = $statusStyles[strtolower($appointment->status)] ?? 'bg-light';
                                @endphp
                                <span class="badge rounded-pill border px-3 fw-bold {{ $currentStyle }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if(strtolower($appointment->status) === 'pending')
                                    {{-- Show Cancel for Pending --}}
                                    <form action="{{ route('pet-owner.appointments.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Cancel</button>
                                    </form>
                                @elseif(strtolower($appointment->status) === 'completed' || strtolower($appointment->status) === 'done')
                                    {{-- Show a "View Summary" or "Rebook" button for finished appointments --}}
                                    <button class="btn btn-orange btn-outline-primary rounded-pill px-3">View</button>
                                @else
                                    {{-- Optional: Keep a fallback or just leave it empty --}}
                                    <span class="text-muted small">Closed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-muted mb-0">No appointments scheduled yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('partials._book_appointment_modal')
@endsection
