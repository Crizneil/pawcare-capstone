<div class="modal fade" id="deleteEmployeeModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <h6 class="fw-bold">Are you sure you want to delete <br><span class="text-primary">{{ $employee->name }}</span>?</h6>
                <p class="text-muted small">This action cannot be undone.</p>

                <form action="{{ route('admin.staff.destroy', ['id' => $employee->id]) }}" method="POST">
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
