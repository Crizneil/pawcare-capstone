<div class="modal fade" id="walkInModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="bi bi-person-plus-fill text-orange me-2"></i>New Walk-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('staff.appointments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="small fw-bold mb-1">Pet Name</label>
                            <input type="text" name="pet_name" class="form-control rounded-3 bg-light border-0" placeholder="e.g. Buddy" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold mb-1">Species</label>
                            <select name="species" class="form-select rounded-3 bg-light border-0">
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Service Required</label>
                        <select name="service_type" class="form-select rounded-3 bg-light border-0">
                            <option value="Vaccination">Vaccination</option>
                            <option value="General Check-up">General Check-up</option>
                            <option value="Deworming">Deworming</option>
                        </select>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="createAccountToggle" name="create_account" value="1">
                        <label class="form-check-label small fw-bold" for="createAccountToggle">Register new owner account?</label>
                    </div>

                    <div id="registrationFields" style="display: none;">
                        <div class="mb-2">
                            <label class="small fw-bold mb-1">Owner Full Name</label>
                            <input type="text" name="owner_name" class="form-control rounded-3 bg-primary-light border-0" placeholder="John Doe">
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold mb-1">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-3 bg-primary-light border-0" placeholder="john@example.com">
                        </div>
                        <div class="mb-0">
                            <label class="small fw-bold mb-1">Phone Number</label>
                            <input type="text" name="phone" class="form-control rounded-3 bg-primary-light border-0" placeholder="09123456789">
                        </div>
                        <div class="alert alert-info mt-2 py-2 border-0 rounded-3" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> Default password will be: <strong>PawCare2026</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-orange w-100 rounded-pill py-2 shadow-sm fw-bold">CREATE APPOINTMENT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle registration fields visibility
    document.getElementById('createAccountToggle').addEventListener('change', function() {
        const fields = document.getElementById('registrationFields');
        fields.style.display = this.checked ? 'block' : 'none';

        // Optional: Make fields required only if toggled
        const inputs = fields.querySelectorAll('input');
        inputs.forEach(input => input.required = this.checked);
    });
</script>
