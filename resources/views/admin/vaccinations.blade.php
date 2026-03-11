@extends('layout.admin')

@section('page_title', 'Vaccine Inventory Dashboard')

@section('content')
<div class="container-fluid p-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1">Vaccine Inventory</h2>
            <p class="text-muted small mb-0">Manage vaccine stock and expiry tracking.</p>
        </div>

        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-dark rounded-pill px-4 py-2 shadow-sm fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Generate Report
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                    <li><a class="dropdown-item py-2" href="{{ route('admin.reports.vaccine', ['type' => 'all']) }}" target="_blank">
                        <i class="bi bi-list-check me-2"></i> Full Inventory Report</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.reports.vaccine', ['type' => 'low_stock']) }}" target="_blank">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i> Low Stock Report</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('admin.reports.vaccine', ['type' => 'expiring']) }}" target="_blank">
                        <i class="bi bi-calendar-x me-2 text-danger"></i> Expiring Vaccines Report</a></li>
                </ul>
            </div>

            <button class="btn btn-orange rounded-pill px-4 py-2 shadow-sm fw-bold"
                    data-bs-toggle="modal"
                    data-bs-target="#addVaccineModal">
                <i class="bi bi-plus-lg me-1"></i> Add Vaccine
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4 p-3">
        <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text border-0 bg-light rounded-start-pill ps-4">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-0 bg-light rounded-end-pill py-2"
                        placeholder="Search by vaccine name or batch number...">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-orange w-100 rounded-pill py-2 fw-bold shadow-sm">Search</button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-mobile-table">
                    <thead class="bg-light">
                        <tr class="text-uppercase small fw-bold text-muted">
                            <th class="ps-4">Vaccine</th>
                            <th>Batch No.</th>
                            <th>Received</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Expiry Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vaccines as $vaccine)
                        @php
                            $isExpired = $vaccine->expiry_date && now()->gt($vaccine->expiry_date);
                            $isOutOfStock = $vaccine->stock <= 0;
                            $isLowStock = !$isOutOfStock && ($vaccine->stock <= $vaccine->low_stock_threshold);
                        @endphp
                        <tr>
                            <td class="ps-4" data-label="Vaccine">
                                <div class="fw-bold text-dark">{{ $vaccine->name }}</div>
                                @if($vaccine->description)
                                    <small class="text-muted text-truncate-custom" style="font-size: 0.75rem;">
                                        {{ $vaccine->description }}
                                    </small>
                                @endif
                            </td>
                            <td data-label="Batch No.">
                                <span class="text-primary fw-medium">{{ $vaccine->batch_no ?? 'N/A' }}</span>
                            </td>
                            <td data-label="Received">
                                <span class="small text-muted">
                                    {{ $vaccine->received_date ? \Carbon\Carbon::parse($vaccine->received_date)->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>
                            <td data-label="Stock">
                                <span class="badge {{ $isOutOfStock ? 'bg-danger' : ($isLowStock ? 'bg-warning text-dark' : 'bg-success') }} rounded-pill px-3">
                                    {{ $vaccine->stock }}
                                </span>
                            </td>
                            <td data-label="Status">
                                @if ($isExpired)
                                    <span class="badge bg-danger-subtle text-danger px-3">Expired</span>
                                @elseif($isOutOfStock)
                                    <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3">Out of Stock</span>
                                @elseif($isLowStock)
                                    <span class="badge bg-warning-subtle text-dark fw-bold border border-warning rounded-pill px-3">Low Stock</span>
                                @else
                                    <span class="badge bg-success-subtle text-success fw-bold border border-success rounded-pill px-3">Normal</span>
                                @endif
                            </td>
                            <td data-label="Expiry Date">
                                <span class="fw-bold">
                                    {{ $vaccine->expiry_date ? \Carbon\Carbon::parse($vaccine->expiry_date)->format('M d, Y') : '--' }}
                                </span>
                            </td>
                            <td class="text-end pe-4 action-cell" data-label="Action">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 fw-medium" type="button" data-bs-toggle="dropdown">
                                        Manage <i class="bi bi-three-dots-vertical ms-1"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                        <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#editVaccineModal{{ $vaccine->id }}">Edit Stock</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item py-2 text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteVaccineModal{{ $vaccine->id }}">Delete Vaccine</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5">No inventory found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $vaccines->appends(request()->query())->links() }}
    </div>
</div>
@foreach($vaccines as $vaccine)
    @include('partials._edit_vaccine_modal')
    @include('partials._delete_vaccine_modal')
@endforeach

    @include('partials._add_vaccine_modal')
@endsection
