<div class="modal fade" id="walkInModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-orange-subtle p-2 rounded-3 me-3">
                        <i data-lucide="walking" class="text-orange"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0">New Walk-in Patient</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('staff.appointments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Pet Basic Info --}}
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Pet Name</label>
                        <input type="text" name="pet_name" class="form-control rounded-pill bg-light border-0 px-3"
                            placeholder="e.g. Buddy" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">Species</label>
                            <select name="species" id="walkinSpeciesSelect"
                                class="form-select rounded-pill bg-light border-0 px-3" required>
                                <option value="" selected disabled>Select Species</option>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">Gender</label>
                            <select name="gender" class="form-select rounded-pill bg-light border-0 px-3" required>
                                <option value="" selected disabled>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Unknown">Unknown</option>
                            </select>
                        </div>
                    </div>

                    {{-- Breed Logic --}}
                    <div id="walkinBreedContainer" class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Breed</label>
                        <select name="breed" id="walkinBreedSelect"
                            class="form-select rounded-pill bg-light border-0 px-3" required>
                            <option value="" selected disabled>Select species first</option>
                        </select>
                    </div>

                    <div id="walkinOtherBreedContainer" class="mb-3 d-none">
                        <label class="small fw-bold text-muted mb-1">Specify Breed</label>
                        <input type="text" name="other_breed" id="walkinOtherBreedInput"
                            class="form-control rounded-pill bg-light border-0 px-3" placeholder="Enter breed">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">Birthdate (Estimate)</label>
                            <input type="date" name="birthday" class="form-control rounded-pill bg-light border-0 px-3"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted mb-1">Service</label>
                            <select name="service_type" class="form-select rounded-pill bg-light border-0 px-3"
                                required>
                                <option value="vaccination">Vaccination</option>
                                <option value="check-up">Check-up</option>
                                <option value="deworming">Deworming</option>
                            </select>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    {{-- Registration Toggle --}}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="walkinCreateAccountToggle"
                            name="create_account" value="1">
                        <label class="form-check-label small fw-bold" for="walkinCreateAccountToggle">Register new owner
                            account?</label>
                    </div>

                    <div id="walkinRegistrationFields" style="display: none;">
                        <div class="mb-2">
                            <label class="small fw-bold text-muted mb-1">Owner Full Name</label>
                            <input type="text" name="owner_name"
                                class="form-control rounded-pill bg-light border-0 px-3" placeholder="John Doe">
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold text-muted mb-1">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-pill bg-light border-0 px-3"
                                placeholder="john@example.com">
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold text-muted mb-1">Phone Number</label>
                            <input type="text" name="phone" class="form-control rounded-pill bg-light border-0 px-3"
                                placeholder="09123456789">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="submit" class="btn btn-orange w-100 rounded-pill py-3 shadow-sm fw-bold">CREATE
                        APPOINTMENT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Initialize Breed Logic for Walk-in Modal
        if (typeof initializePetBreedLogic === 'function') {
            initializePetBreedLogic({
                speciesId: 'walkinSpeciesSelect',
                breedId: 'walkinBreedSelect',
                breedContainerId: 'walkinBreedContainer',
                otherContainerId: 'walkinOtherBreedContainer',
                otherInputId: 'walkinOtherBreedInput'
            });
        }

        // 2. Toggle registration fields
        const toggle = document.getElementById('walkinCreateAccountToggle');
        const fields = document.getElementById('walkinRegistrationFields');
        if (toggle) {
            toggle.addEventListener('change', function () {
                fields.style.display = this.checked ? 'block' : 'none';
                const inputs = fields.querySelectorAll('input');
                inputs.forEach(input => input.required = this.checked);
            });
        }
    });
</script>
