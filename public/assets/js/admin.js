$(document).ready(function () {

    function adminDashboardLogic() {
        var sidebar = $('.admin-sidebar');
        var toggleBtn = $('#sidebarToggle');
        var logoutBtn = $('#logoutBtnSide');
        var logoutForm = $('#logoutForm');

        // Toggle Sidebar on Mobile
        if (toggleBtn.length) {
            toggleBtn.on('click', function (e) {
                sidebar.toggleClass('active');
                e.stopPropagation();
            });
        }

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.admin-sidebar, #sidebarToggle').length && sidebar.hasClass('active')) {
                sidebar.removeClass('active');
            }
        });

        // Handle Logout
        if (logoutBtn.length && logoutForm.length) {
            logoutBtn.on('click', function (e) {
                e.preventDefault();
                logoutForm.submit();
            });
        }
    }

    // --- NEW: Vaccination Auto-Status Logic ---
    function initVaccinationLogic() {
        // Use delegated event listener so it works even if modals are loaded dynamically
        $(document).on('change', 'input[name="next_due_date"]', function () {
            const $input = $(this);
            const $modal = $input.closest('.modal');
            const $statusSelect = $modal.find('select[name="status"]');

            if (!$input.val()) return; // Do nothing if date is cleared

            const dueDate = new Date($input.val());
            const today = new Date();

            // Reset hours for accurate date comparison
            today.setHours(0, 0, 0, 0);
            dueDate.setHours(0, 0, 0, 0);

            // Calculate difference in days
            const diffTime = dueDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                $statusSelect.val('overdue');
            } else if (diffDays <= 14) {
                $statusSelect.val('due_soon');
            } else {
                $statusSelect.val('fully_vaccinated');
            }
        });
    }

    // --- Profile Image Preview Logic ---
    var imageInput = $('#imageInput');
    if (imageInput.length) {
        imageInput.on('change', function () {
            var file = this.files[0];
            var preview = $('#previewImage');

            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Initialize all functions
    adminDashboardLogic();
    initVaccinationLogic();
});
