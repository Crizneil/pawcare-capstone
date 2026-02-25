<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addStaffModalLabel">Create Staff Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.staff.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-pill bg-light border-0 px-3" placeholder="e.g. Juan Dela Cruz" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-pill bg-light border-0 px-3" placeholder="staff@pawcare.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Password</label>
                        <input type="password" name="password" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Min 8 characters" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-pill bg-light border-0 px-3" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-50 rounded-pill fw-bold py-2" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-primary w-50 rounded-pill fw-bold py-2">CREATE ACCOUNT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
