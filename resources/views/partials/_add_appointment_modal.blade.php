<div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark m-0">Create New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.appointments.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <h6 class="text-orange fw-bold mb-3"><i class="bi bi-person-fill me-2"></i>Owner Information</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="small fw-bold text-muted">Select Client</label>
                                <select name="user_id" class="form-control rounded-pill bg-light border-0" required>
                                    <option value="">Select Owner...</option>
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->phone ?? 'No Phone' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="mb-4">
                        <h6 class="text-orange fw-bold mb-3"><i class="bi bi-paw-fill me-2"></i>Pet Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Pet Name</label>
                                <input type="text" name="pet_name" class="form-control rounded-pill bg-light border-0" placeholder="Buddy" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Species</label>
                                <select name="species" class="form-control rounded-pill bg-light border-0" required>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="mb-4">
                        <h6 class="text-orange fw-bold mb-3"><i class="bi bi-clock-fill me-2"></i>Appointment Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Date</label>
                                <input type="date" name="appointment_date" class="form-control rounded-pill bg-light border-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted">Preferred Time</label>
                                <input type="time" name="appointment_time" class="form-control rounded-pill bg-light border-0" required>
                            </div>
                            <div class="col-md-12">
                                <label class="small fw-bold text-muted">Service Type</label>
                                <select name="service_type" class="form-control rounded-pill bg-light border-0" required>
                                    <option value="checkup">General Checkup</option>
                                    <option value="vaccination">Vaccination</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-orange w-100 rounded-pill fw-bold py-3 mt-2 shadow-sm">
                        CONFIRM NEW APPOINTMENT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
