@extends('layout.admin')

@section('page_title', 'Vaccination Status | Staff')

@section('content')
<div class="container-fluid p-4 fade-in">

    {{-- Header & Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Vaccination Tracker</h2>
            <p class="text-muted small mb-0">Monitor immunization schedules and log new vaccinations.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('staff.vaccination-status', request('today') ? [] : ['today' => 1]) }}"
               class="btn {{ request('today') ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-4 shadow-sm">
                <i data-lucide="calendar-check" class="me-2" style="width:16px;"></i>
                {{ request('today') ? 'Showing Today' : 'Vaccinated Today' }}
            </a>

            <form action="{{ route('staff.vaccination-status') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control rounded-pill border-0 shadow-sm px-4"
                       placeholder="Search Pet or Owner...">
                <button class="btn btn-orange rounded-pill px-4 shadow-sm">
                    <i data-lucide="search" style="width:18px;"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- Added 'custom-mobile-table' class to trigger your CSS --}}
                <table class="table table-hover align-middle mb-0 custom-mobile-table">
                    <thead class="bg-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Pet Patient</th>
                            <th>Owner</th>
                            <th>Vaccine Type</th>
                            <th>Administered By</th>
                            <th>Date Given</th>
                            <th>Next Due</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pets as $pet)
                        @php $vax = $pet->latestVaccination; @endphp
                        <tr>
                            {{-- Added data-label for mobile card labels --}}
                            <td class="ps-4" data-label="Pet">
                                <div class="d-flex align-items-center">
                                    <div class="bg-blue-light rounded-circle p-2 me-3 text-primary text-center d-none d-md-block" style="width:40px; height:40px;">
                                        <i data-lucide="dog" style="width:20px;"></i>
                                    </div>
                                    <div class="text-end-mobile">
                                        <div class="fw-bold text-dark">{{ $pet->name }}</div>
                                        @php
                                            // Get the single most recent appointment for this pet
                                            $latestApt = $pet->appointments->sortByDesc('created_at')->first();
                                            $aptStatus = strtolower($latestApt->status ?? '');
                                        @endphp

                                        @if($aptStatus == 'checked-in')
                                            <span class="badge bg-soft-warning text-warning" style="font-size: 0.65rem;">READY FOR SHOT</span>
                                        @elseif(in_array($aptStatus, ['done', 'completed']))
                                            <span class="badge bg-soft-success text-success" style="font-size: 0.65rem;">TREATMENT COMPLETED</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td data-label="Owner">
                                @if($pet->user)
                                    <div class="fw-medium text-dark">{{ $pet->user->name }}</div>
                                    <small class="text-muted">{{ $pet->user->phone }}</small>
                                @else
                                    <div class="fw-medium text-dark">{{ $pet->owner }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary border small" style="font-size: 0.7rem;">Walk-in Guest</span>
                                @endif
                            </td>

                            <td data-label="Vaccine">
                                @if($vax)
                                    <span class="badge bg-info-subtle text-info border border-info px-3">
                                        {{ $vax->vaccine_name }}
                                    </span>
                                @else
                                    <span class="text-muted small italic">None</span>
                                @endif
                            </td>

                            <td data-label="Admin By">
                                <div class="small">
                                    <i data-lucide="user-check" class="text-muted me-1 d-none d-md-inline" style="width:14px;"></i>
                                    {{ $vax->staff->name ?? 'System' }}
                                </div>
                            </td>

                            <td class="small" data-label="Date Given">
                                {{ $vax ? \Carbon\Carbon::parse($vax->date_administered)->format('M d, Y') : '--' }}
                            </td>

                            <td data-label="Next Due">
                                @if($vax && $vax->next_due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($vax->next_due_date);
                                        $today = \Carbon\Carbon::today();
                                        $daysRemaining = $today->diffInDays($dueDate, false);

                                        $isOverdue = $daysRemaining < 0;
                                        $isDueSoon = $daysRemaining >= 0 && $daysRemaining <= 14;
                                        $isUpToDate = $daysRemaining > 14;
                                    @endphp

                                    <div @class([
                                        'small fw-bold d-inline-flex align-items-center',
                                        'text-danger' => $isOverdue,
                                        'text-warning' => $isDueSoon,
                                        'text-success' => $isUpToDate,
                                    ])>
                                        {{ $dueDate->format('M d, Y') }}
                                    </div>
                                @else
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>

                            {{-- This 'Actions' label is critical for the alignment fix --}}
                            <td class="text-end pe-4" data-label="Actions">
                                <button class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateVax{{ $pet->id }}">
                                    <i data-lucide="plus-circle" class="me-1" style="width:14px;"></i> Log Shot
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-lucide="clipboard-x" class="mb-3" style="width:48px; height:48px; opacity: 0.5;"></i>
                                    <h5 class="fw-bold">No Authorized Patients Found</h5>
                                    <p class="small">Only pets with <b>Approved Appointments</b> appear here.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $pets->appends(request()->query())->links() }}
    </div>
</div>

@foreach($pets as $pet)
    @include('partials._vax_modal')
@endforeach
@endsection
