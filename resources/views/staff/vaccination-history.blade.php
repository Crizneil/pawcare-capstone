@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <h2 class="fw-bold mb-4">Vaccination History</h2>

    {{-- 1. FILTER NOTICE: Shows only when a specific pet is selected --}}
    @if(request('pet_id'))
        @php $petName = $history->first()->pet->name ?? 'this pet'; @endphp
        <div class="alert {{ $history->isEmpty() ? 'alert-warning' : 'alert-primary' }} border-0 shadow-sm rounded-4 d-flex justify-content-between align-items-center mb-4 p-3">
            <div>
                <i data-lucide="{{ $history->isEmpty() ? 'alert-triangle' : 'info' }}" class="me-2" style="width: 20px;"></i>
                @if($history->isEmpty())
                    No vaccination history found for this pet.
                @else
                    Currently showing records for: <strong>{{ $petName }}</strong>
                @endif
            </div>
            <a href="{{ route('staff.vaccination-history') }}" class="btn btn-sm {{ $history->isEmpty() ? 'btn-warning' : 'btn-primary' }} rounded-pill px-3">
                <i data-lucide="x-circle" class="me-1" style="width:14px;"></i> View All Records
            </a>
        </div>
    @endif

    {{-- 2. ADVANCED FILTER BAR: Needs a hidden input --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('staff.vaccination-history') }}" method="GET" class="row g-2 align-items-center">

                {{-- IMPORTANT: Keep the pet_id filter active even when changing other dropdowns --}}
                @if(request('pet_id'))
                    <input type="hidden" name="pet_id" value="{{ request('pet_id') }}">
                @endif

                {{-- Quick Date Filters --}}
                <div class="col-md-auto">
                    <div class="btn-group rounded-pill overflow-hidden border shadow-sm" role="group">
                        <a href="{{ route('staff.vaccination-history', ['filter' => 'today']) }}"
                           class="btn btn-sm {{ request('filter') == 'today' ? 'btn-primary' : 'btn-white' }}">Today</a>
                        <a href="{{ route('staff.vaccination-history', ['filter' => 'week']) }}"
                           class="btn btn-sm {{ request('filter') == 'week' ? 'btn-primary' : 'btn-white' }}">This Week</a>
                        <a href="{{ route('staff.vaccination-history') }}"
                           class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-white' }}">All Time</a>
                    </div>
                </div>

                {{-- By Staff --}}
                <div class="col-md-2">
                    <select name="staff_id" class="form-select form-select-sm border-0 bg-light rounded-pill shadow-sm px-3" onchange="this.form.submit()">
                        <option value="">All Staff</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- By Vaccine --}}
                <div class="col-md-2">
                    <select name="vaccine_name" class="form-select form-select-sm border-0 bg-light rounded-pill shadow-sm px-3" onchange="this.form.submit()">
                        <option value="">All Vaccines</option>
                        @foreach($vaccineList as $vax)
                            <option value="{{ $vax->name }}" {{ request('vaccine_name') == $vax->name ? 'selected' : '' }}>
                                {{ $vax->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Button --}}
                <div class="col-md-auto">
                    <a href="{{ route('staff.vaccination-history') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i data-lucide="refresh-cw" class="me-1" style="width:14px;"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- History Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small fw-bold text-uppercase">
                        <th class="ps-4 py-3">Date Administered</th>
                        <th>Pet Name</th>
                        <th>Vaccine Type</th>
                        <th>Batch No.</th>
                        <th>Administered By</th>
                        <th>Next Due Date</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $record)
                        @php
                            $today = \Carbon\Carbon::today();
                            $dueDate = $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date) : null;

                            // Logic for the Auto-Generated Status
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
                            {{-- 1. Date Administered --}}
                            <td class="ps-4">
                                {{ \Carbon\Carbon::parse($record->date_administered)->format('M d, Y') }}
                            </td>

                            {{-- 2. Pet Name --}}
                            <td>
                                <div class="fw-bold text-dark">{{ $record->pet->name }}</div>
                                <small class="text-muted">#{{ $record->pet_id }}</small>
                            </td>

                            {{-- 3. Vaccine Type --}}
                            <td>
                                <span class="badge bg-blue-light text-primary">{{ $record->vaccine_name }}</span>
                            </td>

                            {{-- 4. Batch No. --}}
                            <td>
                                <code class="text-muted small fw-bold">{{ $record->batch_no ?? 'N/A' }}</code>
                            </td>

                            {{-- 5. Administered By --}}
                            <td>
                                <div class="small fw-medium">
                                    <i data-lucide="user-check" class="text-muted me-1" style="width:14px;"></i>
                                    {{ $record->staff->name ?? 'System' }}</div>
                            </td>

                            {{-- 6. Next Due Date --}}
                            <td>
                                <span class="small {{ $statusLabel === 'Overdue' ?  : 'text-dark' }}">
                                    {{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('M d, Y') : '--' }}
                                </span>
                            </td>

                            {{-- 7. Status (Auto Generated) --}}
                            <td class="text-end pe-4">
                                <span class="badge rounded-pill border {{ $statusClass }} px-3">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i data-lucide="calendar-x" class="mb-2" style="width: 40px; height: 40px;"></i>
                                <p>No vaccination records found for this filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
