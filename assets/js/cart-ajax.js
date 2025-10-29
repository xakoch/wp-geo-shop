/**
 * AJAX Cart Updates
 */
(function($) {
    'use strict';

    // Проверяем наличие blockUI
    if (typeof $.fn.block === 'undefined') {
        // Простая заглушка если blockUI не загружен
        $.fn.block = function() { return this; };
        $.fn.unblock = function() { return this; };
    }

    // Модальное окно для подтверждения удаления
    const CartModal = {
        $modal: null,
        $overlay: null,
        $content: null,
        currentCartItemKey: null,

        init: function() {
            this.$modal = $('#cart-delete-modal');
            this.$overlay = this.$modal.find('.cart-modal__overlay');
            this.$content = this.$modal.find('.cart-modal__content');

            // Закрытие модального окна
            this.$modal.on('click', '.cart-modal__close, .cart-modal__cancel', () => this.close());
            this.$overlay.on('click', () => this.close());

            // Подтверждение удаления
            this.$modal.on('click', '.cart-modal__confirm', () => this.confirm());

            // Закрытие по ESC
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.$modal.is(':visible')) {
                    this.close();
                }
            });
        },

        open: function(cartItemKey) {
            this.currentCartItemKey = cartItemKey;
            this.$modal.fadeIn(200);
            // Добавляем класс для анимации
            setTimeout(() => {
                this.$content.addClass('cart-modal__content--active');
            }, 10);
        },

        close: function() {
            this.$content.removeClass('cart-modal__content--active');
            setTimeout(() => {
                this.$modal.fadeOut(200);
            }, 200);
        },

        confirm: function() {
            if (this.currentCartItemKey) {
                removeFromCart(this.currentCartItemKey);
                this.close();
            }
        }
    };

    // Обновление количества товара через AJAX
    function updateCartQuantity(cartItemKey, newQuantity) {
        const $quantityWrapper = $(`.cart-item__quantity[data-cart-item-key="${cartItemKey}"]`);
        const $cartItem = $quantityWrapper.closest('.cart-item');

        console.log('Updating quantity:', cartItemKey, newQuantity);

        // Блокируем элемент
        $quantityWrapper.addClass('updating');

        // AJAX запрос на обновление количества
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: 'update_cart_item_qty',
                cart_item_key: cartItemKey,
                quantity: newQuantity
            },
            success: function(response) {
                console.log('Update response:', response);
                $quantityWrapper.removeClass('updating');

                if (response.success) {
                    const data = response.data;

                    // Обновляем отображение количества
                    $quantityWrapper.find('.qty-display').text(data.quantity);
                    $quantityWrapper.find('.qty-value').val(data.quantity);

                    // Обновляем цену товара (line total)
                    $cartItem.find('.cart-item__price').html(data.line_total);

                    // Обновляем общую сумму корзины
                    $('.cart-totals__price').html(data.cart_total);

                    // Обновляем фрагменты (мини-корзину и счетчик)
                    if (data.fragments) {
                        $.each(data.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    // Триггерим событие обновления корзины
                    $(document.body).trigger('updated_cart_totals');
                    $(document.body).trigger('wc_fragment_refresh');

                    // Показываем уведомление об успехе
                    showNotification('Cart updated', 'success');
                } else {
                    showNotification(response.data.message || 'Error updating cart', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Update error:', error);
                $quantityWrapper.removeClass('updating');
                showNotification('Error updating cart', 'error');
            }
        });
    }

    // Удаление товара из корзины через AJAX
    function removeFromCart(cartItemKey) {
        const $cartItem = $(`.cart-item[data-cart-item-key="${cartItemKey}"]`);

        console.log('Removing item:', cartItemKey);

        // Блокируем элемент
        $cartItem.addClass('removing');

        // AJAX запрос на удаление
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: 'remove_cart_item',
                cart_item_key: cartItemKey
            },
            success: function(response) {
                console.log('Remove response:', response);

                if (response.success) {
                    const data = response.data;

                    // Показываем уведомление об успехе
                    showNotification('Item removed from cart', 'success');

                    // Анимация удаления товара
                    $cartItem.slideUp(300, function() {
                        $(this).remove();

                        // Если корзина пуста, перезагружаем страницу для показа cart-empty
                        if (data.cart_is_empty) {
                            setTimeout(() => {
                                location.reload();
                            }, 300);
                        } else {
                            // Обновляем totals без перезагрузки
                            updateCartTotals();
                        }
                    });

                    // Обновляем фрагменты (мини-корзину и счетчик)
                    if (data.fragments) {
                        $.each(data.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    // Триггерим событие обновления корзины
                    $(document.body).trigger('updated_cart_totals');
                    $(document.body).trigger('wc_fragment_refresh');
                } else {
                    console.error('Remove failed:', response.data);
                    $cartItem.removeClass('removing');
                    showNotification(response.data.message || 'Error removing item', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Remove cart item AJAX error:', error, xhr.responseText);
                $cartItem.removeClass('removing');
                showNotification('Error removing item from cart', 'error');
            }
        });
    }

    // Обновление totals корзины через AJAX
    function updateCartTotals() {
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: 'woocommerce_get_refreshed_fragments'
            },
            success: function(response) {
                if (response && response.fragments) {
                    // Обновляем cart totals
                    $.each(response.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                }
            }
        });
    }

    // Показать уведомление
    function showNotification(message, type = 'info') {
        // Удаляем предыдущее уведомление если есть
        $('.cart-notification').remove();

        const $notification = $('<div>', {
            class: `cart-notification cart-notification--${type}`,
            text: message
        });

        $('body').append($notification);

        // Показываем уведомление
        setTimeout(() => {
            $notification.addClass('cart-notification--show');
        }, 10);

        // Скрываем через 3 секунды
        setTimeout(() => {
            $notification.removeClass('cart-notification--show');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        }, 3000);
    }

    // Инициализация при загрузке
    $(document).ready(function() {

        // Инициализируем модальное окно
        CartModal.init();

        // Кнопки +/- для изменения количества
        $(document).on('click', '.cart-item__quantity .qty-btn', function(e) {
            e.preventDefault();

            const $button = $(this);
            const $quantityWrapper = $button.closest('.cart-item__quantity');
            const $display = $quantityWrapper.find('.qty-display');
            const cartItemKey = $quantityWrapper.data('cart-item-key');

            let currentVal = parseInt($display.text()) || 1;
            const min = parseInt($quantityWrapper.data('min')) || 1;
            let maxAttr = parseInt($quantityWrapper.data('max'));

            // В WooCommerce, max = -1 означает неограниченное количество
            const max = (maxAttr && maxAttr > 0) ? maxAttr : 999999;
            let newVal = currentVal;

            console.log('Qty button clicked:', {
                currentVal: currentVal,
                min: min,
                max: max,
                maxAttr: maxAttr,
                button: $button.hasClass('qty-btn--plus') ? 'plus' : 'minus'
            });

            if ($button.hasClass('qty-btn--plus')) {
                newVal = currentVal + 1;
                if (max !== 999999 && newVal > max) {
                    showNotification('Maximum quantity reached: ' + max, 'warning');
                    return;
                }
            } else if ($button.hasClass('qty-btn--minus')) {
                newVal = currentVal - 1;
                if (newVal < min) {
                    showNotification('Minimum quantity is ' + min, 'warning');
                    return;
                }
            }

            if (newVal !== currentVal && cartItemKey) {
                // Обновляем количество через AJAX
                updateCartQuantity(cartItemKey, newVal);
            }
        });

        // Удаление товара - показываем модальное окно
        $(document).on('click', 'a.remove_from_cart_button, a.ajax_remove_from_cart', function(e) {
            e.preventDefault();

            const $link = $(this);

            // Пробуем разные способы получить cart_item_key
            let cartItemKey = $link.data('cart_item_key') || $link.attr('data-cart-item-key');

            console.log('Remove button clicked');
            console.log('Link element:', $link[0]);
            console.log('data-cart_item_key (jQuery .data()):', $link.data('cart_item_key'));
            console.log('data-cart-item-key (attr):', $link.attr('data-cart-item-key'));
            console.log('All data attributes:', $link[0].dataset);
            console.log('Final cartItemKey:', cartItemKey);

            if (cartItemKey) {
                // Открываем модальное окно для подтверждения
                CartModal.open(cartItemKey);
            } else {
                console.error('ERROR: Cart item key not found!');
                showNotification('Error: Cart item key not found', 'error');
            }
        });

    });

})(jQuery);
