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
        const $cartItem = $(`.cart-item[data-cart-item-key="${cartItemKey}"]`);
        const $quantityWrapper = $cartItem.find('.cart-item__quantity');

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
                $quantityWrapper.removeClass('updating');

                if (response.success) {
                    const data = response.data;

                    // Обновляем input количества
                    $quantityWrapper.find('input.qty').val(data.quantity);

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
                    showNotification('Cart updated successfully', 'success');
                } else {
                    showNotification(response.data.message || 'Error updating cart', 'error');
                }
            },
            error: function() {
                $quantityWrapper.removeClass('updating');
                showNotification('Error updating cart. Please try again.', 'error');
            }
        });
    }

    // Удаление товара из корзины через AJAX
    function removeFromCart(cartItemKey) {
        const $cartItem = $(`.cart-item[data-cart-item-key="${cartItemKey}"]`);

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
                if (response.success) {
                    const data = response.data;

                    // Анимация удаления товара
                    $cartItem.slideUp(300, function() {
                        $(this).remove();

                        // Если корзина пуста, перезагружаем страницу
                        if (data.cart_is_empty) {
                            location.reload();
                        }
                    });

                    // Обновляем фрагменты (мини-корзину и счетчик)
                    if (data.fragments) {
                        $.each(data.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    // Обновляем общую сумму корзины
                    if (!data.cart_is_empty) {
                        // Триггерим событие обновления корзины для пересчета totals
                        $(document.body).trigger('updated_cart_totals');
                        $(document.body).trigger('wc_fragment_refresh');

                        // Перезагружаем для корректного обновления totals
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }

                    // Показываем уведомление об успехе
                    showNotification('Item removed from cart', 'success');
                } else {
                    $cartItem.removeClass('removing');
                    showNotification(response.data.message || 'Error removing item', 'error');
                }
            },
            error: function() {
                $cartItem.removeClass('removing');
                showNotification('Error removing item from cart', 'error');
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
            const $input = $quantityWrapper.find('input.qty');
            const cartItemKey = $quantityWrapper.data('cart-item-key');

            let currentVal = parseInt($input.val()) || 1;
            const max = parseInt($input.attr('max')) || 9999;
            const min = parseInt($input.attr('min')) || 1;
            let newVal = currentVal;

            if ($button.hasClass('plus')) {
                newVal = currentVal + 1;
                if (newVal > max) {
                    showNotification('Maximum quantity reached', 'warning');
                    return;
                }
            } else if ($button.hasClass('minus')) {
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

        // Изменение количества вручную (ввод в input)
        let qtyUpdateTimer;
        $(document).on('input', '.cart-item__quantity input.qty', function() {
            const $input = $(this);
            const cartItemKey = $input.data('cart-item-key');
            let newVal = parseInt($input.val()) || 1;
            const min = parseInt($input.attr('min')) || 1;
            const max = parseInt($input.attr('max')) || 9999;

            // Проверяем границы
            if (newVal < min) newVal = min;
            if (newVal > max) newVal = max;

            // Обновляем значение если оно было скорректировано
            if (newVal !== parseInt($input.val())) {
                $input.val(newVal);
            }

            // Дебаунс для избежания множественных запросов
            clearTimeout(qtyUpdateTimer);
            qtyUpdateTimer = setTimeout(function() {
                if (cartItemKey) {
                    updateCartQuantity(cartItemKey, newVal);
                }
            }, 800);
        });

        // Удаление товара - показываем модальное окно
        $(document).on('click', 'a.remove_from_cart_button, a.ajax_remove_from_cart', function(e) {
            e.preventDefault();

            const $link = $(this);
            const cartItemKey = $link.data('cart_item_key');

            if (cartItemKey) {
                // Открываем модальное окно для подтверждения
                CartModal.open(cartItemKey);
            } else {
                showNotification('Error: Cart item key not found', 'error');
            }
        });

    });

})(jQuery);
