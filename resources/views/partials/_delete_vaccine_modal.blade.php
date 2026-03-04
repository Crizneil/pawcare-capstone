<div class="modal fade" id="deleteVaccineModal{{ $vaccine->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <h6 class="fw-bold">Delete <span class="text-primary">{{ $vaccine->name }}</span>?</h6>
                <p class="text-muted small">Batch: {{ $vaccine->batch_no }}<br>This cannot be undone.</p>

                <form action="{{ route('admin.vaccine.destroy', $vaccine->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-danger rounded-pill fw-bold">DELETE</button>
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
