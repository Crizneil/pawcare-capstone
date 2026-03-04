(function ($) {
    "use strict";

    $(document).ready(function () {

        /* ======================================================
           1. SIDEBAR & NAVIGATION (Public & Admin Unified)
        ====================================================== */
        const $sidebar = $('#sidebar');
        const $overlay = $('#sidebarOverlay');
        const $body = $('body');
        const $navHolder = $(".navigation-holder");

        function toggleMenu() {
            const isOpen = $sidebar.hasClass('active');

            if (isOpen) {
                // CLOSE LOGIC
                $sidebar.removeClass('active');
                $overlay.fadeOut(300).removeClass('active');
                $body.removeClass('overflow-hidden');
                $navHolder.removeClass("slideInn");
                $('#sidebarToggle, .open-btn, .navbar-toggler').removeClass('x-close');
            } else {
                // OPEN LOGIC
                $sidebar.addClass('active');
                $overlay.fadeIn(300).addClass('active');
                $body.addClass('overflow-hidden');
                $navHolder.addClass("slideInn");
                $('#sidebarToggle, .open-btn, .navbar-toggler').addClass('x-close');
            }
        }

        // Single Click Listener for Toggle Button
        $(document).on('click', '#sidebarToggle, .open-btn, .navbar-toggler', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleMenu(); // This now handles both open and close
        });

        // Close listeners
        $(document).on('click', '#closeSidebar, #sidebarOverlay, .menu-close', function () {
            if ($sidebar.hasClass('active')) {
                toggleMenu();
            }
        });

        // Close sidebar if window is resized to Desktop
        $(window).on('resize', function () {
            if ($(window).width() > 991) {
                handleMenu('close');
            }
        });

        // Ensure sidebar closes when a Modal opens (Avoids Z-index overlap)
        $('.modal').on('show.bs.modal', function () {
            handleMenu('close');
        });

        /* ======================================================
           2. VACCINATION AUTO-STATUS LOGIC
        ====================================================== */
        $(document).on('change', 'input[name="next_due_date"]', function () {
            const $input = $(this);
            const $modal = $input.closest('.modal');
            const $statusSelect = $modal.find('select[name="status"]');

            if (!$input.val()) return;

            const dueDate = new Date($input.val());
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            dueDate.setHours(0, 0, 0, 0);

            const diffTime = dueDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // Auto-select status based on date
            if (diffDays < 0) {
                $statusSelect.val('overdue');
            } else if (diffDays <= 14) {
                $statusSelect.val('due_soon');
            } else {
                $statusSelect.val('fully_vaccinated');
            }
        });

        /* ======================================================
           3. PROFILE IMAGE PREVIEW
        ====================================================== */
        $(document).on('change', '#imageInput', function () {
            const file = this.files[0];
            const $preview = $('#previewImage');
            if (file) {
                const reader = new FileReader();
                reader.onload = e => $preview.attr('src', e.target.result);
                reader.readAsDataURL(file);
            }
        });

        /* ======================================================
           4. UI ENHANCEMENTS (Back to Top & Data Tables)
        ====================================================== */
        // Back to Top
        $(window).on("scroll", function () {
            if ($(window).scrollTop() > 700) {
                $(".back-to-top").fadeIn();
            } else {
                $(".back-to-top").fadeOut();
            }
        });

        $(document).on('click', '.back-to-top', function () {
            $("html, body").animate({ scrollTop: 0 }, 700);
            return false;
        });

        // Logout Form Trigger
        $(document).on('click', '#logoutBtnSide', function (e) {
            e.preventDefault();
            $('#logoutForm').submit();
        });
    });
    let lastScrollTop = 0;
    const $header = $('header');

    $(window).on('scroll', function() {
        let st = $(this).scrollTop();

        if (st > lastScrollTop && st > 100) {
            // Scrolling Down - Hide Header
            $header.css('transform', 'translateY(-100%)');
        } else {
            // Scrolling Up - Show Header
            $header.css('transform', 'translateY(0)');
        }
        lastScrollTop = st;
    });
})(window.jQuery);
