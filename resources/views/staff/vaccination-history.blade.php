@extends('layout.admin')

@section('page_title', 'Vaccination History | Staff')

@section('content')
<div class="container-fluid p-4 fade-in">
    <h2 class="fw-bold mb-4 text-dark">Vaccination History</h2>

    {{-- 1. FILTER NOTICE --}}
    @if(request('pet_id'))
        @php $petName = $history->first()->pet->name ?? 'this pet'; @endphp
        <div class="alert {{ $history->isEmpty() ? 'alert-warning' : 'alert-primary' }} border-0 shadow-sm rounded-4 d-flex justify-content-between align-items-center mb-4 p-3">
            <div class="small">
                <i data-lucide="{{ $history->isEmpty() ? 'alert-triangle' : 'info' }}" class="me-2 text-primary" style="width: 20px;"></i>
                @if($history->isEmpty())
                    No vaccination history found for this pet.
                @else
                    Currently showing records for: <strong>{{ $petName }}</strong>
                @endif
            </div>
            <a href="{{ route('staff.vaccination-history') }}" class="btn btn-sm {{ $history->isEmpty() ? 'btn-warning' : 'btn-primary' }} rounded-pill px-3 shadow-sm">
                <i data-lucide="x-circle" class="me-1" style="width:14px;"></i> View All
            </a>
        </div>
    @endif

    {{-- 2. ADVANCED FILTER BAR --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('staff.vaccination-history') }}" method="GET" class="row g-3 align-items-center">
                @if(request('pet_id'))
                    <input type="hidden" name="pet_id" value="{{ request('pet_id') }}">
                @endif

                {{-- Quick Date Filters --}}
                <div class="col-12 col-xl-auto">
                    <div class="btn-group rounded-pill overflow-hidden border shadow-sm w-100" role="group">
                        <a href="{{ route('staff.vaccination-history', ['filter' => 'today']) }}"
                           class="btn btn-sm {{ request('filter') == 'today' ? 'btn-primary' : 'btn-white' }}">Today</a>
                        <a href="{{ route('staff.vaccination-history', ['filter' => 'week']) }}"
                           class="btn btn-sm {{ request('filter') == 'week' ? 'btn-primary' : 'btn-white' }}">This Week</a>
                        <a href="{{ route('staff.vaccination-history') }}"
                           class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-white' }}">All Time</a>
                    </div>
                </div>

                {{-- By Staff --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <select name="staff_id" class="form-select form-select-sm border-0 bg-light rounded-pill shadow-sm px-3 py-2" onchange="this.form.submit()">
                        <option value="">All Staff</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- By Vaccine --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <select name="vaccine_name" class="form-select form-select-sm border-0 bg-light rounded-pill shadow-sm px-3 py-2" onchange="this.form.submit()">
                        <option value="">All Vaccines</option>
                        @foreach($vaccineList as $vax)
                            <option value="{{ $vax->name }}" {{ request('vaccine_name') == $vax->name ? 'selected' : '' }}>
                                {{ $vax->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Button --}}
                <div class="col-12 col-md-auto ms-md-auto">
                    <a href="{{ route('staff.vaccination-history') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4 w-100 shadow-sm">
                        <i data-lucide="refresh-cw" class="me-1" style="width:14px;"></i> Reset Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. HISTORY TABLE --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-mobile-table">
                <thead class="bg-light text-secondary small fw-bold text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Date</th>
                        <th>Pet Name</th>
                        <th>Vaccine</th>
                        <th>Batch</th>
                        <th>Staff</th>
                        <th>Next Due</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $record)
                        @php
                            $today = \Carbon\Carbon::today();
                            $dueDate = $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date) : null;

                            $statusLabel = 'Up to Date';
                            $statusClass = 'bg-success-subtle text-success border-success';

                            if ($dueDate) {
                                $daysUntilDue = $today->diffInDays($dueDate, false);
                                if ($daysUntilDue < 0) {
                                    $statusLabel = 'Overdue';
                                    $statusClass = 'bg-danger-subtle text-danger border-danger';
                                } elseif ($daysUntilDue <= 14) {
                                    $statusLabel = 'Due Soon';
                                    $statusClass = 'bg-warning-subtle text-warning border-warning';
                                }
                            }
                        @endphp
                        <tr>
                            <td class="ps-4" data-label="Date">
                                <div class="fw-medium text-dark">
                                    {{ \Carbon\Carbon::parse($record->date_administered)->format('M d, Y') }}
                                </div>
                            </td>

                            <td data-label="Pet Name">
                                <div class="fw-bold text-primary">{{ $record->pet->name }}</div>
                                <small class="text-muted">ID: #{{ $record->pet_id }}</small>
                            </td>

                            <td data-label="Vaccine">
                                <span class="badge bg-blue-light text-primary px-3 rounded-pill border border-primary-subtle">
                                    {{ $record->vaccine_name }}
                                </span>
                            </td>

                            <td data-label="Batch">
                                <code class="text-secondary small fw-bold">{{ $record->batch_no ?? 'N/A' }}</code>
                            </td>

                            <td data-label="Staff">
                                <div class="small fw-medium">
                                    <i data-lucide="user-check" class="text-muted me-1 d-none d-md-inline" style="width:14px;"></i>
                                    {{ $record->staff->name ?? 'System' }}
                                </div>
                            </td>

                            <td data-label="Next Due">
                                <span class="small @if($statusLabel === 'Overdue') text-danger fw-bold @else text-dark @endif">
                                    {{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('M d, Y') : '--' }}
                                </span>
                            </td>

                            <td class="text-end pe-4" data-label="Status">
                                <span class="badge rounded-pill border {{ $statusClass }} px-3 py-1 shadow-xs" style="min-width: 90px;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i data-lucide="calendar-x" class="mb-2 opacity-50" style="width: 48px; height: 48px;"></i>
                                    <p class="mb-0">No vaccination records found for this selection.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
