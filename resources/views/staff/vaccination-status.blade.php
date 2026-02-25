@extends('layouts.admin')

@section('page_title', 'Vaccination Status | Staff')

@section('content')
<div class="container-fluid p-4 fade-in">

    {{-- Header & Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Vaccination Tracker</h2>
            <p class="text-muted small mb-0">Monitor immunization schedules and log new vaccinations.</p>
        </div>

        <div class="d-flex gap-2">
            {{-- Vaccinated Today Filter Toggle --}}
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
                <table class="table table-hover align-middle mb-0">
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
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-blue-light rounded-circle p-2 me-3 text-primary text-center" style="width:40px; height:40px;">
                                        <i data-lucide="dog" style="width:20px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $pet->name }}</div>
                                        <small class="text-muted">ID: #{{ $pet->id }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($pet->user)
                                    <div class="fw-medium text-dark">{{ $pet->user->name }}</div>
                                    <small class="text-muted">{{ $pet->user->phone }}</small>
                                @else
                                    <div class="fw-medium text-dark">{{ $pet->owner }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary border small" style="font-size: 0.7rem;">Walk-in Guest</span>
                                @endif
                            </td>

                            <td>
                                @if($vax)
                                    <span class="badge bg-info-subtle text-info border border-info px-3">
                                        {{ $vax->vaccine_name }}
                                    </span>
                                @else
                                    <span class="text-muted small italic text-decoration-line-through">None</span>
                                @endif
                            </td>

                            <td>
                                <div class="small">
                                    {{-- Assuming the Vaccination model has a relationship to the staff user --}}
                                    <i data-lucide="user-check" class="text-muted me-1" style="width:14px;"></i>
                                    {{ $vax->staff->name ?? 'System' }}
                                </div>
                            </td>

                            <td class="small">
                                {{ $vax ? \Carbon\Carbon::parse($vax->date_administered)->format('M d, Y') : '--' }}
                            </td>

                            <td>
                                @if($vax && $vax->next_due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($vax->next_due_date);
                                        $today = \Carbon\Carbon::today();
                                        $daysRemaining = $today->diffInDays($dueDate, false);

                                        // Logic definitions
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

                            <td class="text-end pe-4 action-cell">
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
                                    <i data-lucide="syringe" class="mb-2" style="width:48px; height:48px; opacity: 0.5;"></i>
                                    <p>No vaccination records found matching your filters.</p>
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
