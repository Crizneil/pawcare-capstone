<div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark m-0">Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.staff.update', ['id' => $employee->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3 text-start">
                        <label class="small fw-bold text-muted">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-pill bg-light border-0" value="{{ $employee->name }}" required>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="small fw-bold text-muted">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-pill bg-light border-0" value="{{ $employee->email }}" required>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="small fw-bold text-muted">New Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control rounded-pill bg-light border-0" placeholder="Min 8 characters">
                    </div>
                    <div class="mb-3 text-start">
                        <label class="small fw-bold text-muted">Confirm New Password</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control rounded-pill bg-light border-0"
                               placeholder="Confirm password">
                    </div>

                    <button type="submit" class="btn btn-orange w-100 rounded-pill fw-bold py-2">UPDATE ACCOUNT</button>
                </form>
            </div>
        </div>
    </div>
</div>
