@extends('layout.admin')

@section('page_title', 'Vaccine Inventory | Staff')

@section('content')
<div class="container-fluid p-4 fade-in">

    {{-- 1. Top Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-bold text-uppercase">Total Stock</div>
                <div class="h3 fw-bold mb-0 text-primary">{{ $vaccines->sum('stock') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-bold text-warning text-uppercase">Low Stock</div>
                <div class="h3 fw-bold mb-0">{{ $vaccines->where('stock', '<=', 10)->where('expiry_date', '>=', now())->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-bold text-info text-uppercase">Expiring</div>
                <div class="h3 fw-bold mb-0">
                    {{ $vaccines->where('expiry_date', '>', now())->where('expiry_date', '<=', now()->addMonths(2))->count() }}
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="text-muted small fw-bold text-danger text-uppercase">Expired</div>
                <div class="h3 fw-bold mb-0">{{ $vaccines->where('expiry_date', '<', now())->count() }}</div>
            </div>
        </div>
    </div>

    {{-- 2. Header with Search Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Vaccine Inventory</h2>
            <p class="text-muted small mb-0">Review batch details and update stock status.</p>
        </div>

        <div class="search-container" style="min-width: 300px;">
            <form action="{{ url()->current() }}" method="GET">
                <div class="input-group bg-white rounded-pill shadow-sm overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 px-3"
                           placeholder="Search Batch or Vaccine..." value="{{ request('search') }}">
                    <button class="btn btn-white border-0 text-primary px-3" type="submit">
                        <i data-lucide="search" style="width: 18px;"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. Main Inventory Table --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-mobile-table">
                    <thead class="bg-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Vaccine Name</th>
                            <th>Batch No.</th>
                            <th>Stock</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vaccines as $vaccine)
                        <tr>
                            <td class="ps-4" data-label="Vaccine">
                                <div class="fw-bold text-dark">{{ $vaccine->name }}</div>
                            </td>

                            <td data-label="Batch No.">
                                <code class="text-muted small fw-bold">{{ $vaccine->batch_no }}</code>
                            </td>

                            <td data-label="Stock">
                                <span @class([
                                    'fw-bold',
                                    'text-danger' => $vaccine->stock <= 5,
                                    'text-warning' => $vaccine->stock > 5 && $vaccine->stock <= 15,
                                    'text-dark' => $vaccine->stock > 15
                                ])>
                                    {{ $vaccine->stock }}
                                </span>
                            </td>

                            <td data-label="Expiry">
                                <span class="small">{{ \Carbon\Carbon::parse($vaccine->expiry_date)->format('M d, Y') }}</span>
                            </td>

                            <td data-label="Status">
                                @php
                                    $expiry = \Carbon\Carbon::parse($vaccine->expiry_date);
                                    $isExpired = $expiry->isPast();
                                    $isLow = $vaccine->stock <= 10;
                                @endphp

                                @if($isExpired)
                                    <span class="badge bg-danger-subtle text-danger px-3 rounded-pill border border-danger">Expired</span>
                                @elseif($isLow)
                                    <span class="badge bg-warning-subtle text-warning px-3 rounded-pill border border-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill border border-success">In Stock</span>
                                @endif
                            </td>

                            <td class="small text-muted" data-label="Updated">
                                {{ $vaccine->updated_at->format('M d, Y') }}
                            </td>

                            <td class="text-end pe-4" data-label="Actions">
                                <button type="button"
                                        class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewVaccine{{ $vaccine->id }}">
                                    <i data-lucide="eye" class="me-1" style="width: 14px;"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i data-lucide="package-search" class="text-muted mb-2 opacity-50" style="width: 48px; height: 48px;"></i>
                                <p class="text-muted">No inventory records found matching your search.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach ($vaccines as $vaccine)
    @include('partials._view_vaccine_modal')
@endforeach
@endsection
