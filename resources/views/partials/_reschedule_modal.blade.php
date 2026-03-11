<div class="modal fade" id="rescheduleModal{{ $appointment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-calendar-event-fill text-primary me-2"></i>Reschedule Visit
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('staff.appointments.reschedule', $appointment->id) }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small fw-bold text-uppercase text-muted mb-2 d-block">New Appointment Date</label>
                            <input type="date" name="appointment_date" class="form-control rounded-pill border-light-subtle shadow-sm" value="{{ $appointment->appointment_date }}" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-uppercase text-muted mb-2 d-block">New Appointment Time</label>
                            <input type="time" name="appointment_time" class="form-control rounded-pill border-light-subtle shadow-sm" value="{{ $appointment->appointment_time }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-orange rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
