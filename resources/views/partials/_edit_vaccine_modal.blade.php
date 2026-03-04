<div class="modal fade" id="editVaccineModal{{ $vaccine->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('admin.vaccine.update', $vaccine->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold">Update Inventory: {{ $vaccine->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Batch Number</label>
                        <input type="text" name="batch_no" class="form-control" value="{{ $vaccine->batch_no }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Current Stock</label>
                            <input type="number" name="stock" class="form-control" value="{{ $vaccine->stock }}" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ $vaccine->expiry_date ? \Carbon\Carbon::parse($vaccine->expiry_date)->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-orange rounded-pill px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
