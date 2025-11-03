/**
 * Authentication - Login/Register Sidebar
 */
(function($) {
    'use strict';

    // Account Sidebar Toggle
    var $accountSidebar = $('.account-sidebar');
    var $accountOverlay = $('.account-overlay');
    var $accountClose = $('.account-close');

    function openAccountSidebar() {
        $accountSidebar.addClass('active');
        $('body').addClass('account-open');
        if (window.lenis) {
            window.lenis.stop();
        }
    }

    function closeAccountSidebar() {
        $accountSidebar.removeClass('active');
        $('body').removeClass('account-open');
        if (window.lenis) {
            window.lenis.start();
        }
    }

    // Remove old handlers from add.min.js
    $('.account-link').off('click');
    $('.login-form').off('submit');
    $('.register-form').off('submit');
    $('.account-tab').off('click');
    $accountClose.off('click');
    $accountOverlay.off('click');

    // Open account sidebar
    $('.account-link').on('click', function(e) {
        e.preventDefault();
        openAccountSidebar();
    });

    // Close account sidebar
    $accountClose.on('click', closeAccountSidebar);
    $accountOverlay.on('click', closeAccountSidebar);

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $accountSidebar.hasClass('active')) {
            closeAccountSidebar();
        }
    });

    // Tab switching
    $('.account-tab').on('click', function() {
        var tab = $(this).data('tab');

        $('.account-tab').removeClass('active');
        $(this).addClass('active');

        $('.account-form-wrapper').removeClass('active');
        $('#' + tab + '-form').addClass('active');
    });

    // Error notification function (for errors only)
    function showNotification(message, isError) {
        if (!isError) {
            return; // Use modal for success messages
        }

        $('.cart-notification').remove();

        var notification = $('<div class="cart-notification error">' +
            '<span class="notification-icon">✕</span>' +
            '<span class="notification-text">' + message + '</span>' +
        '</div>');

        $('body').append(notification);

        setTimeout(function() {
            notification.addClass('show');
        }, 100);

        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Success modal function
    function showSuccessModal(title, message) {
        var $modal = $('.success-modal');

        // Update content
        $modal.find('.success-modal-title').text(title);
        $modal.find('.success-modal-message').text(message);

        // Show modal
        $modal.addClass('active');
        $('body').addClass('modal-open');

        // Stop scrolling
        if (window.lenis) {
            window.lenis.stop();
        }

        // Close account sidebar
        closeAccountSidebar();

        // Auto close after 2.5 seconds and reload
        setTimeout(function() {
            closeSuccessModal();
        }, 2500);
    }

    function closeSuccessModal() {
        var $modal = $('.success-modal');
        $modal.removeClass('active');
        $('body').removeClass('modal-open');

        // Resume scrolling
        if (window.lenis) {
            window.lenis.start();
        }

        setTimeout(function() {
            location.reload();
        }, 300);
    }

    // Close modal on overlay click
    $(document).on('click', '.success-modal-overlay', function() {
        closeSuccessModal();
    });

    // Close modal on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('.success-modal').hasClass('active')) {
            closeSuccessModal();
        }
    });

    // AJAX Login
    $('.login-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var formData = $form.serialize();

        formData += '&action=ajax_login';

        $button.prop('disabled', true).text('Loggin...');

        $.ajax({
            type: 'POST',
            url: authParams.ajax_url,
            data: formData,
            success: function(response) {
                if (response.success) {
                    showSuccessModal('Welcome!', response.data.message);
                } else {
                    showNotification(response.data, true);
                    $button.prop('disabled', false).text('Login');
                }
            },
            error: function() {
                showNotification('An error has occurred. Try again later.', true);
                $button.prop('disabled', false).text('Login');
            }
        });
    });

    // AJAX Register
    $('.register-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var formData = $form.serialize();

        formData += '&action=ajax_register';

        $button.prop('disabled', true).text('Register...');

        $.ajax({
            type: 'POST',
            url: authParams.ajax_url,
            data: formData,
            success: function(response) {
                console.log('Register response:', response);

                if (response.success) {
                    showSuccessModal('Registration is completed!', response.data.message);
                } else {
                    showNotification(response.data || 'An error occurred during registration.', true);
                    $button.prop('disabled', false).text('Register');
                }
            },
            error: function(xhr, status, error) {
                console.error('Register error:', xhr, status, error);
                showNotification('An error has occurred. Try again later.', true);
                $button.prop('disabled', false).text('Register');
            }
        });
    });

})(jQuery);
