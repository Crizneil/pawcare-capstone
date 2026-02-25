<div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark m-0">Book Vaccination Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Updated Action to match the new route --}}
                <form action="{{ route('pet-owner.appointments.book') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Select Your Pet</label>
                        <select name="pet_id" class="form-control rounded-pill bg-light border-0" required>
                            <option value="">Which pet is visiting?</option>
                            @foreach(Auth::user()->pets as $pet)
                                <option value="{{ $pet->id }}">{{ $pet->name }} ({{ ucfirst($pet->species) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Preferred Date</label>
                            <input type="date" name="appointment_date"
                                class="form-control rounded-pill bg-light border-0" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Preferred Time</label>
                            <input type="time" name="appointment_time"
                                class="form-control rounded-pill bg-light border-0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted">Service Requested</label>
                        <select name="service_type" class="form-control rounded-pill bg-light border-0" required>
                            <option value="vaccination">Vaccination (Standard)</option>
                            <option value="week_1">Week 1 (Initial Visit)</option>
                            <option value="booster">Booster Shot</option>
                            <option value="checkup">Checkup & Vaccination</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-orange w-100 rounded-pill fw-bold py-3 shadow-sm">
                        REQUEST APPOINTMENT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
