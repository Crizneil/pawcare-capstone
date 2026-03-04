@extends('layout.admin')

@section('content')
    <div class="container-fluid p-4 fade-in">
        {{-- Header - Added flex-wrap for mobile --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0">Appointments</h2>
                <p class="text-muted mb-0 small">Manage walk-ins and scheduled vaccinations.</p>
            </div>
            <button class="btn btn-orange rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#walkInModal">
                <i data-lucide="plus" class="me-2" style="width: 18px;"></i> New Walk-in
            </button>
        </div>

        {{-- Sub-menu Navigation - Modern Segmented Control --}}
        <div class="d-flex justify-content-center mb-4">
            <div class="bg-light rounded-pill p-1 shadow-sm d-inline-flex flex-wrap" style="border: 1px solid #e9ecef;">
                <a class="btn rounded-pill px-4 fw-bold {{ $view == 'today' ? 'btn-primary text-white shadow-sm' : 'btn-light text-muted border-0' }}"
                    href="{{ route('staff.appointments', ['view' => 'today']) }}" style="transition: all 0.2s;">
                    <i data-lucide="calendar" class="me-1 d-none d-sm-inline-block" style="width: 16px;"></i> Today
                </a>
                <a class="btn rounded-pill px-4 fw-bold {{ $view == 'upcoming' ? 'btn-primary text-white shadow-sm' : 'btn-light text-muted border-0' }}"
                    href="{{ route('staff.appointments', ['view' => 'upcoming']) }}" style="transition: all 0.2s;">
                    <i data-lucide="clock" class="me-1 d-none d-sm-inline-block" style="width: 16px;"></i> Upcoming
                </a>
                <a class="btn rounded-pill px-4 fw-bold {{ $view == 'completed' ? 'btn-primary text-white shadow-sm' : 'btn-light text-muted border-0' }}"
                    href="{{ route('staff.appointments', ['view' => 'completed']) }}" style="transition: all 0.2s;">
                    <i data-lucide="check-circle" class="me-1 d-none d-sm-inline-block" style="width: 16px;"></i> Completed
                </a>
            </div>
        </div>

        {{-- Appointment List --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                {{-- Added custom-mobile-table --}}
                <table class="table table-hover align-middle mb-0 custom-mobile-table">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
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
                            @php
                                $isCompleted = in_array(strtolower($apt->status), ['done', 'completed']);
                                $isApproved = in_array(strtolower($apt->status), ['approved', 'rescheduled', 'pending']);
                            @endphp
                            <tr>
                                <td class="ps-4" data-label="Pet Details">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden flex-shrink-0"
                                            style="width: 45px; height: 45px;">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($apt->pet_name) }}&background=random&color=fff&rounded=true"
                                                alt="Pet" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $apt->pet_name }}</div>
                                            <small class="text-muted text-uppercase">{{ $apt->species }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Owner">
                                    @if($apt->user_id)
                                        <div class="fw-bold text-dark">{{ $apt->user->name }}</div>
                                        <small class="text-muted"><i class="bi bi-person-check"></i> Member</small>
                                    @else
                                        <div class="fw-bold text-muted small">Guest</div>
                                    @endif
                                </td>

                                <td data-label="Service">
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill">
                                        {{ $apt->service_type ?? 'General' }}
                                    </span>
                                </td>

                                <td data-label="Schedule">
                                    <div class="small fw-bold text-dark">{{ date('M d, Y', strtotime($apt->appointment_date)) }}
                                    </div>
                                    <div class="text-muted small">{{ date('h:i A', strtotime($apt->appointment_time)) }}</div>
                                </td>

                                <td data-label="Status">
                                    @if($apt->status == 'pending')
                                        <span
                                            class="badge rounded-pill bg-warning-subtle text-warning border border-warning px-3">Pending</span>
                                    @elseif($apt->status == 'approved' || $apt->status == 'rescheduled')
                                        <span
                                            class="badge rounded-pill bg-info-subtle text-info border border-info px-3">Approved</span>
                                    @elseif($apt->status == 'Done' || $apt->status == 'completed')
                                        <span
                                            class="badge rounded-pill bg-success-subtle text-success border border-success px-3">Completed</span>
                                    @endif
                                </td>

                                {{-- Actions Column --}}
                                <td class="text-end pe-4" data-label="Actions">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm" type="button"
                                            data-bs-toggle="dropdown">
                                            Manage <i data-lucide="more-vertical" class="ms-1" style="width: 14px;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                            @if($isApproved)
                                                @if(strtolower($apt->service_type) !== 'checkup')
                                                    <li>
                                                        <a class="dropdown-item py-2"
                                                            href="{{ route('staff.vaccination-status', ['search' => $apt->pet_name, 'apt_id' => $apt->id]) }}">
                                                            <i data-lucide="syringe" class="me-2 text-primary" style="width: 16px;"></i>
                                                            Start Vaccination
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <button type="button" class="dropdown-item py-2 text-success"
                                                        data-bs-toggle="modal" data-bs-target="#confirmDoneModal{{ $apt->id }}">
                                                        <i data-lucide="check-circle" class="me-2" style="width: 16px;"></i> Mark as
                                                        Completed
                                                    </button>
                                                </li>
                                            @elseif($isCompleted)
                                                <li>
                                                    <button class="dropdown-item py-2" data-bs-toggle="modal"
                                                        data-bs-target="#viewResultModal{{ $apt->id }}">
                                                        <i data-lucide="eye" class="me-2 text-info" style="width: 16px;"></i> View
                                                        Results
                                                    </button>
                                                </li>
                                            @else
                                                <li><span class="dropdown-item-text text-muted small">No actions available</span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No records found for this view.</td>
                            </tr>
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

    {{-- Modals Loop --}}
    @foreach($appointments as $apt)
        @php
            $isCompleted = in_array(strtolower($apt->status), ['done', 'completed']);
            $isApproved = in_array(strtolower($apt->status), ['approved', 'rescheduled', 'pending']);
        @endphp

        @if($isCompleted)
            @include('partials._view_appointment_result_modal', ['appointment' => $apt])
        @endif

        @if($isApproved)
            @include('partials._confirm_done_modal', ['appointment' => $apt])
        @endif
    @endforeach
    @include('partials._walk-in_modal')

    @push('scripts')
        <script src="{{ asset('assets/js/pet-registration.js') }}?v={{ time() }}"></script>
    @endpush
@endsection
