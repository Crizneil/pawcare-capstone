@extends('layout.admin')

@section('page_title', 'Overview')

@section('content')
    <div class="container-fluid animate__animated animate__fadeIn">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Overview Dashboard</h3>
            <div class="d-flex align-items-center">
                <span class="badge bg-white shadow-sm text-dark p-2 rounded-pill me-3">
                    <i data-lucide="calendar" class="size-14 me-1"></i> {{ date('F d, Y') }}
                </span>

                <!-- Reporting System Dropdown -->
                <div class="btn-group">
                    <button type="button" class="btn btn-dark rounded-pill fw-bold px-4 shadow-sm dropdown-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i data-lucide="file-bar-chart" class="me-2 size-16"></i> Generate Report
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-4">
                        <li>
                            <h6 class="dropdown-header fw-bold text-uppercase small text-muted">Analysis Reports</h6>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2"
                                href="{{ route('admin.reports.appointments') }}">
                                <i data-lucide="calendar-check" class="text-primary me-2 size-16"></i> Appointment Records
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2"
                                href="{{ route('admin.reports.inventory') }}">
                                <i data-lucide="truck" class="text-danger me-2 size-16"></i> Stock & Inventory Movement
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-lg p-4 mb-4 bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-white">
                            <i data-lucide="scan" class="me-2"></i> Quick Patient Scan
                        </h5>
                        <span class="badge bg-warning text-dark">HARDWARE COMPATIBLE</span>
                    </div>
                    <form action="{{ route('admin.search-pet') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="search" id="scanInput"
                                class="form-control form-control-lg border-0 bg-light" placeholder="Scan QR or Enter ID..."
                                autofocus autocomplete="off">
                            <button class="btn btn-warning px-4 fw-bold" type="submit">SEARCH</button>
                        </div>
                        <small class="text-white-50 mt-2 d-block">Instant redirection to pet medical records upon
                            scan.</small>
                    </form>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-4 text-center h-100">
                            <h6 class="text-muted text-uppercase fw-bold small">Total Patients</h6>
                            <h2 class="display-5 fw-bold text-dark mb-0">{{ $totalPets ?? 0 }}</h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-4 text-center h-100" style="border-left: 5px solid #d35400;">
                            <h6 class="text-uppercase fw-bold small" style="color: #d35400;">Active Staff Members</h6>
                            <h2 class="display-5 fw-bold mb-0" style="color: #d35400;">{{ $totalStaff ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                {{-- Low Stock Alert --}}
                @if(isset($lowStockVaccines) && $lowStockVaccines->count() > 0)
                    <div class="alert alert-danger border-0 shadow-sm rounded-lg d-flex align-items-center mb-4">
                        <i data-lucide="alert-triangle" class="me-3 size-24"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Low Inventory Alert</h6>
                            <p class="small mb-0">The following items are running low on stock:
                                <strong>{{ $lowStockVaccines->pluck('name')->implode(', ') }}</strong>.
                                Please restock soon to avoid shortages.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-lg p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Verification Requests from Staff</h5>
                        <span class="badge bg-primary rounded-pill">{{ count($requests ?? []) }} PENDING</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small fw-bold text-uppercase">Pet</th>
                                    <th class="small fw-bold text-uppercase">Requested By</th>
                                    <th class="small fw-bold text-uppercase">Date</th>
                                    <th class="text-end small fw-bold text-uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @forelse($requests ?? [] as $req)
                                                @php /** @var \App\Models\UserRequest $req */ @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $req?->pet?->name ?? 'Unknown' }}</div>
                                                <small class="text-muted">{{ $req?->pet?->unique_id ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ $req?->requester?->name ?? 'Unknown' }}</td>
                                            <td>{{ $req?->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('admin.requests.update', $req?->id ?? 0) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit"
                                                        class="btn btn-success btn-sm rounded-pill px-3 me-2 small">APPROVE</button>
                                                </form>
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 small"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $req?->id }}">REJECT</button>
                                            </td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i data-lucide="inbox" class="text-muted mb-2"
                                                style="width: 48px; height: 48px;"></i>
                                            <p class="text-muted mb-0">No pending digital card requests.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm rounded-lg d-flex align-items-center">
                    <i data-lucide="info" class="me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-0">System Status</h6>
                        <small>All systems operational. {{ $totalOwners ?? 0 }} registered owners in database.</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Logic for Rejection Modals --}}
        @foreach($requests ?? [] as $req)
                    @php /** @var \App\Models\UserRequest $req */ @endphp
            <div class="modal fade" id="rejectModal{{ $req?->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold">Reject Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.requests.update', $req?->id ?? 0) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <div class="modal-body">
                                <label class="small fw-bold text-uppercase text-muted">Reason for Rejection</label>
                                <textarea name="remarks" class="form-control bg-light border-0 mt-2" rows="3"
                                    placeholder="Explain why..." required></textarea>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-link text-muted text-decoration-none"
                                    data-bs-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4">CONFIRM REJECTION</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    <script>
        const scanInput = document.getElementById('scanInput');

        // Keep focus on the input so the scanner always types here
        window.addEventListener('load', () => scanInput.focus());
        document.addEventListener('click', () => {
            setTimeout(() => scanInput.focus(), 2000);
        });

        // Detect when the scanner types a URL and auto- submit
        scanInput.addEventListener('input', function () {
            // If the scanner types the full URL from the QR code
            if (this.value.includes('http')) {
                // Give the hardware a tiny moment to finish the text string
                setTimeout(() => {
                    this.form.submit();
                }, 500);
            }
        });
    </script>
@endsection
