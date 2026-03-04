<div class="modal fade" id="rejectModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i>Reject Appointment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.appointments.reject', $appointment->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <p class="text-muted">You are about to reject the appointment for <strong>{{ $appointment->pet_name }}</strong>. This action will notify the owner.</p>
                    <div class="mb-3">
                        <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Reason for Rejection</label>
                        <textarea name="rejection_reason" class="form-control border-light-subtle shadow-sm" rows="3" placeholder="e.g., Doctor is unavailable on this date..." style="border-radius: 1rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Go Back</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
