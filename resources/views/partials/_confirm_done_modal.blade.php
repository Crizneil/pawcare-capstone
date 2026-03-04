<div class="modal fade" id="confirmDoneModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i data-lucide="check-circle" style="width: 30px; height: 30px;"></i>
                </div>
                <h5 class="fw-bold">Mark as Completed?</h5>
                <p class="text-muted small">Are you sure you want to mark <strong>{{ $appointment->pet_name }}'s</strong> appointment as done?</p>

                <form action="{{ route('staff.appointments.update', [$appointment->id, 'Done']) }}" method="POST">
                    @csrf
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success rounded-pill fw-bold">Yes, Complete it</button>
                        <button type="button" class="btn btn-light rounded-pill fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
