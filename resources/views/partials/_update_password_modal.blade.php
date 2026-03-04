<div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pet-owner.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Ensure your account is using a long, random password to stay
                        secure.</p>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Current Password</label>
                            <input type="password" name="old_password"
                                class="form-control rounded-pill border-light bg-light toggle-password" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">New Password</label>
                            <input type="password" name="new_password"
                                class="form-control rounded-pill border-light bg-light toggle-password" required>
                            <small class="text-muted ms-2" style="font-size: 0.75rem;">Minimum 8 characters.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Confirm New
                                Password</label>
                            <input type="password" name="new_password_confirmation"
                                class="form-control rounded-pill border-light bg-light toggle-password" required>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="form-check ms-1">
                                <input class="form-check-input shadow-none cursor-pointer" type="checkbox"
                                    id="showPasswordsToggle">
                                <label class="form-check-label text-muted small cursor-pointer user-select-none"
                                    for="showPasswordsToggle">
                                    Show passwords
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleCheckbox = document.getElementById('showPasswordsToggle');
        const passwordInputs = document.querySelectorAll('.toggle-password');

        if (toggleCheckbox) {
            toggleCheckbox.addEventListener('change', function () {
                const type = this.checked ? 'text' : 'password';
                passwordInputs.forEach(input => {
                    input.type = type;
                });
            });
        }

        // Reset toggle when modal is closed
        const pwdModal = document.getElementById('updatePasswordModal');
        if (pwdModal) {
            pwdModal.addEventListener('hidden.bs.modal', function () {
                if (toggleCheckbox.checked) {
                    toggleCheckbox.click();
                }
            });
        }
    });
</script>
