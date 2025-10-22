jQuery(function($) {

    // if (typeof wc_add_to_cart_params === 'undefined') {
    //     return false;
    // }

    // ========== MINI CART TOGGLE ==========
    var $miniCart = $('.mini-cart-sidebar');
    var $miniCartOverlay = $('.mini-cart-overlay');
    var $miniCartClose = $('.mini-cart-close');
    var $cartLink = $('.cart-link');

    function openMiniCart() {
        $miniCart.addClass('active');
        $('body').addClass('mini-cart-open');
        // Останавливаем Lenis для предотвращения скролла body
        if (window.lenis) {
            window.lenis.stop();
        }
    }

    function closeMiniCart() {
        $miniCart.removeClass('active');
        $('body').removeClass('mini-cart-open');
        // Возобновляем Lenis после закрытия
        if (window.lenis) {
            window.lenis.start();
        }
    }

    $cartLink.on('click', function(e) {
        e.preventDefault();
        openMiniCart();
    });

    $miniCartClose.on('click', closeMiniCart);
    $miniCartOverlay.on('click', closeMiniCart);

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $miniCart.hasClass('active')) {
            closeMiniCart();
        }
    });

    // ========== AJAX NOTIFICATIONS & ACTIONS ==========

    // Открытие мини-корзины после добавления товара
    $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
        showNotification($button.closest('.product').find('.woocommerce-loop-product__title').text() || 'Товар');
        
        // Открываем мини-корзину с небольшой задержкой
        setTimeout(function() {
            openMiniCart();
        }, 300);

        // Анимация счётчика
        $('.cart-count, .mini-cart-count').addClass('bounce');
        setTimeout(function() {
            $('.cart-count, .mini-cart-count').removeClass('bounce');
        }, 600);
    });

    // Функция показа уведомления
    function showNotification(productName, isError) {
        // Удаляем предыдущие уведомления
        $('.cart-notification').remove();

        var notificationClass = isError ? 'cart-notification error' : 'cart-notification success';
        var icon = isError ? '✕' : '✓';
        var message = isError ? productName : (productName + ' добавлен в корзину');

        var $notification = $('<div class="' + notificationClass + '">' +
            '<span class="notification-icon">' + icon + '</span>' +
            '<span class="notification-text">' + message + '</span>' +
        '</div>');

        $('body').append($notification);

        // Показываем с анимацией
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);

        // Скрываем и удаляем через 3 секунды
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }


    // ========== ACCOUNT SIDEBAR TOGGLE ==========
    var $accountSidebar = $('.account-sidebar');
    var $accountOverlay = $('.account-overlay');
    var $accountClose = $('.account-close');
    var $accountLink = $('.account-link');

    function openAccountSidebar() {
        $accountSidebar.addClass('active');
        $('body').addClass('account-open');
        // Останавливаем Lenis для предотвращения скролла body
        if (window.lenis) {
            window.lenis.stop();
        }
    }

    function closeAccountSidebar() {
        $accountSidebar.removeClass('active');
        $('body').removeClass('account-open');
        // Возобновляем Lenis после закрытия
        if (window.lenis) {
            window.lenis.start();
        }
    }

    $accountLink.on('click', function(e) {
        e.preventDefault();
        openAccountSidebar();
    });

    $accountClose.on('click', closeAccountSidebar);
    $accountOverlay.on('click', closeAccountSidebar);

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $accountSidebar.hasClass('active')) {
            closeAccountSidebar();
        }
    });

    // ========== ACCOUNT TABS ==========
    $('.account-tab').on('click', function() {
        var tab = $(this).data('tab');

        $('.account-tab').removeClass('active');
        $(this).addClass('active');

        $('.account-form-wrapper').removeClass('active');
        $('#' + tab + '-form').addClass('active');
    });

    // ========== AJAX ДЛЯ СТРАНИЦЫ ТОВАРА ==========
    $(document).on('click', '.single_add_to_cart_button:not(.disabled)', function(e) {
        var $button = $(this);
        var $form = $button.closest('form.cart');

        // Проверяем, что это простой товар (не вариативный)
        if ($form.length && !$form.hasClass('variations_form')) {
            e.preventDefault();

            var product_id = $form.find('input[name=add-to-cart]').val() || $button.val();
            var quantity = $form.find('input[name=quantity]').val() || 1;

            // Добавляем состояние загрузки
            $button.addClass('loading').prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: wc_add_to_cart_params.ajax_url,
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: product_id,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    // Триггерим событие для обновления корзины
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);

                    // Показываем уведомление
                    var productName = $form.closest('.product').find('.product_title').text() || 'Товар';
                    showNotification(productName);

                    // Открываем мини-корзину
                    setTimeout(function() {
                        openMiniCart();
                    }, 300);

                    // Убираем состояние загрузки
                    $button.removeClass('loading').addClass('added').prop('disabled', false);

                    // Через 2 секунды убираем класс "added"
                    setTimeout(function() {
                        $button.removeClass('added');
                    }, 2000);
                },
                error: function(error) {
                    console.log(error);
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        }
    });

    // ========== AJAX LOGIN ==========
    $('.login-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var formData = $form.serialize();
        formData += '&action=ajax_login';

        $button.prop('disabled', true).text('Вход...');

        $.ajax({
            type: 'POST',
            url: 'https://deeppink-horse-920297.hostingersite.com/wp-admin/admin-ajax.php',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showNotification(response.data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(response.data, true);
                    $button.prop('disabled', false).text('Войти');
                }
            },
            error: function() {
                showNotification('Произошла ошибка. Попробуйте позже.', true);
                $button.prop('disabled', false).text('Войти');
            }
        });
    });

    // ========== AJAX REGISTER ==========
    $('.register-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var formData = $form.serialize();
        formData += '&action=ajax_register';

        $button.prop('disabled', true).text('Регистрация...');

        $.ajax({
            type: 'POST',
            url: 'https://deeppink-horse-920297.hostingersite.com/wp-admin/admin-ajax.php',
            data: formData,
            success: function(response) {
                console.log('Register response:', response);
                if (response.success) {
                    showNotification(response.data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    var errorMessage = response.data || 'Произошла ошибка при регистрации.';
                    showNotification(errorMessage, true);
                    $button.prop('disabled', false).text('Зарегистрироваться');
                }
            },
            error: function(xhr, status, error) {
                console.error('Register error:', xhr, status, error);
                showNotification('Произошла ошибка. Попробуйте позже.', true);
                $button.prop('disabled', false).text('Зарегистрироваться');
            }
        });
    });
});