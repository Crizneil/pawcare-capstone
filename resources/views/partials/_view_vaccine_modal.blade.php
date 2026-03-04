<div class="modal fade" id="viewVaccine{{ $vaccine->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark ps-2 pt-2">Full Vaccine Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Vaccine Name</label>
                    <h4 class="fw-bold text-dark mb-0">{{ $vaccine->name }}</h4>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Batch Number</label>
                        <code class="fs-6 fw-bold text-dark">{{ $vaccine->batch_no }}</code>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Recommended Species</label>
                        <span class="fw-bold text-dark text-capitalize px-3">
                            {{ $vaccine->species ?? 'Dog / Cat' }}
                        </span>
                    </div>
                </div>

                <hr class="text-muted opacity-25">

                <div class="row g-3 mb-4 mt-1">
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Date Received</label>
                        <span class="fw-bold text-dark">{{ $vaccine->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase d-block mb-1">Expiry Date</label>
                        <span @class([
                            'fw-bold',
                            'text-danger' => \Carbon\Carbon::parse($vaccine->expiry_date)->isPast(),
                            'text-dark' => !\Carbon\Carbon::parse($vaccine->expiry_date)->isPast()
                        ])>
                            {{ \Carbon\Carbon::parse($vaccine->expiry_date)->format('M d, Y') }}
                        </span>
                    </div>
                </div>

                <div class="bg-light rounded-4 p-3 border">
                    <h6 class="fw-bold mb-3 small text-uppercase text-muted">Stock Monitoring</h6>
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">Current</small>
                            <span class="h5 fw-bold mb-0">{{ $vaccine->stock }}</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted d-block">Minimum</small>
                            <span class="h5 fw-bold mb-0 text-muted">10</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Status</small>
                            @php
                                $expiry = \Carbon\Carbon::parse($vaccine->expiry_date);
                                $isExpired = $expiry->isPast();
                                $isLow = $vaccine->stock <= 10;
                            @endphp

                            @if($isExpired)
                                <span class="text-danger fw-bold small">Expired</span>
                            @elseif($isLow)
                                <span class="text-warning fw-bold small">Low Stock</span>
                            @else
                                <span class="text-success fw-bold small">In Stock</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
