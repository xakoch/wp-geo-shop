/**
 * Header Dropdown Menu
 * Handles dropdown functionality for mobile devices
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Mobile dropdown toggle
        $('.menu-dropdown-toggle').on('click', function(e) {
            // Only on mobile
            if (window.innerWidth <= 768) {
                e.preventDefault();
                $(this).closest('.menu-item-has-dropdown').toggleClass('active');
            }
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.menu-item-has-dropdown').length) {
                $('.menu-item-has-dropdown').removeClass('active');
            }
        });

        // Close dropdown on window resize if switching to desktop
        $(window).on('resize', function() {
            if (window.innerWidth > 768) {
                $('.menu-item-has-dropdown').removeClass('active');
            }
        });
    });

})(jQuery);
