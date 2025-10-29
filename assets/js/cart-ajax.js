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

    // Обновление количества товара
    function updateCartQuantity($input) {
        var $form = $input.closest('form.woocommerce-cart-form');
        
        // Блокируем форму
        $form.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        // Получаем данные формы
        var formData = $form.serialize();

        // AJAX запрос
        $.ajax({
            type: 'POST',
            url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_cart'),
            data: formData,
            dataType: 'html',
            success: function(response) {
                // Обновляем всю страницу корзины
                var $newCart = $(response).find('.cart-page');
                if ($newCart.length) {
                    $('.cart-page').replaceWith($newCart);
                    
                    // Триггерим событие обновления корзины
                    $(document.body).trigger('updated_cart_totals');
                    $(document.body).trigger('wc_fragment_refresh');
                }
                
                $form.unblock();
            },
            error: function() {
                $form.unblock();
                alert('Error updating cart. Please refresh the page.');
            }
        });
    }

    // Удаление товара из корзины
    function removeFromCart($link) {
        var productId = $link.data('product_id');
        var cartItemKey = $link.data('cart_item_key');
        
        // Блокируем элемент
        $link.closest('.cart-item').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        // AJAX запрос на удаление
        $.ajax({
            type: 'POST',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: 'remove_cart_item',
                product_id: productId,
                cart_item_key: cartItemKey
            },
            success: function(response) {
                if (response.success) {
                    // Обновляем фрагменты корзины
                    $(document.body).trigger('wc_fragment_refresh');
                    
                    // Перезагружаем страницу для обновления
                    location.reload();
                } else {
                    alert('Error removing item from cart.');
                    $link.closest('.cart-item').unblock();
                }
            },
            error: function() {
                alert('Error removing item from cart.');
                $link.closest('.cart-item').unblock();
            }
        });
    }

    // Инициализация при загрузке
    $(document).ready(function() {
        
        // Кнопки +/-
        $(document).on('click', '.cart-item__quantity .qty-btn', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $input = $button.siblings('input.qty');
            var currentVal = parseInt($input.val()) || 1;
            var max = parseInt($input.attr('max')) || 9999;
            var min = parseInt($input.attr('min')) || 1;
            var newVal = currentVal;

            if ($button.hasClass('plus')) {
                newVal = currentVal + 1;
                if (newVal > max) newVal = max;
            } else if ($button.hasClass('minus')) {
                newVal = currentVal - 1;
                if (newVal < min) newVal = min;
            }

            if (newVal !== currentVal) {
                $input.val(newVal).trigger('change');
            }
        });

        // Изменение количества вручную
        $(document).on('change', '.cart-item__quantity input.qty', function() {
            var $input = $(this);
            
            // Небольшая задержка для дебаунса
            clearTimeout($input.data('timer'));
            $input.data('timer', setTimeout(function() {
                updateCartQuantity($input);
            }, 500));
        });

        // Удаление товара
        $(document).on('click', 'a.remove_from_cart_button, a.ajax_remove_from_cart', function(e) {
            e.preventDefault();
            
            var $link = $(this);
            
            // Подтверждение удаления (опционально)
            if (confirm('Remove this item from cart?')) {
                removeFromCart($link);
            }
        });

    });

})(jQuery);