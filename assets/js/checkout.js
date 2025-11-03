/**
 * Checkout Page JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {

        // Disable Select2 on country fields
        $(document.body).on('country_to_state_changing', function() {
            $('#billing_country, #shipping_country').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });
        });

        // Prevent Select2 initialization
        $('#billing_country, #shipping_country').each(function() {
            $(this).removeClass('select2-hidden-accessible');
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });

        // Payment method switching
        $('.payment-method input[type="radio"]').on('change', function() {
            const $paymentMethod = $(this).closest('.payment-method');

            // Remove active class from all payment methods
            $('.payment-method').removeClass('active');

            // Add active class to selected payment method
            $paymentMethod.addClass('active');

            // Update SVG icons
            updatePaymentIcons();
        });

        // Click on label to select payment method
        $('.payment-method label').on('click', function(e) {
            const $input = $(this).siblings('input[type="radio"]');
            if (!$input.is(':checked')) {
                $input.prop('checked', true).trigger('change');
            }
        });

        function updatePaymentIcons() {
            $('.payment-method').each(function() {
                const $method = $(this);
                const $radio = $method.find('input[type="radio"]');
                const $svg = $method.find('.payment-method__icon svg');
                const isActive = $method.hasClass('active');

                // Update circle stroke color
                $svg.find('circle:first-child').attr('stroke', isActive ? '#176DAA' : '#000');

                // Show/hide inner circle
                if (isActive) {
                    if ($svg.find('circle').length === 1) {
                        $svg.append('<circle cx="10" cy="10" r="5" fill="#176DAA"/>');
                    }
                } else {
                    $svg.find('circle:last-child').remove();
                }
            });
        }

        // Initialize on page load
        updatePaymentIcons();

    });

})(jQuery);
