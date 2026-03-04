<div class="modal fade" id="addVaccineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.vaccine.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-box-seam me-2 text-orange"></i>Add New Vaccine Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">VACCINE NAME</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter vaccine name..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">BATCH NUMBER</label>
                        <input type="text" name="batch_no" class="form-control" placeholder="e.g. ARB-2026-001" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">INITIAL STOCK</label>
                            <input type="number" name="stock" class="form-control" placeholder="0" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">LOW STOCK ALERT</label>
                            <input type="number" name="low_stock_threshold" class="form-control" value="10" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">EXPIRY DATE</label>
                        <input type="date" name="expiry_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-orange rounded-pill px-4 shadow-sm fw-bold">Save Inventory</button>
                </div>
            </form>
        </div>
    </div>
</div>
