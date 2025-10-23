/**
 * Quantity +/- buttons for WooCommerce
 */
(function() {
    'use strict';

    function initQuantityButtons() {
        // Находим все поля количества
        const quantityInputs = document.querySelectorAll('input.qty, input[type="number"].input-text.qty');

        quantityInputs.forEach(input => {
            // Проверяем, не добавлены ли уже кнопки
            if (input.parentElement.querySelector('.qty-btn')) {
                return;
            }

            // Создаем кнопку минус
            const minusBtn = document.createElement('button');
            minusBtn.type = 'button';
            minusBtn.className = 'qty-btn minus';
            minusBtn.innerHTML = '−';

            // Создаем кнопку плюс
            const plusBtn = document.createElement('button');
            plusBtn.type = 'button';
            plusBtn.className = 'qty-btn plus';
            plusBtn.innerHTML = '+';

            // Вставляем кнопки
            input.parentElement.insertBefore(minusBtn, input);
            input.parentElement.appendChild(plusBtn);

            // Получаем min, max и step из атрибутов input
            const min = parseFloat(input.getAttribute('min')) || 1;
            const max = parseFloat(input.getAttribute('max')) || Infinity;
            const step = parseFloat(input.getAttribute('step')) || 1;

            // Обработчик для кнопки минус
            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let currentValue = parseFloat(input.value) || min;
                let newValue = currentValue - step;

                if (newValue >= min) {
                    input.value = newValue;
                    // Триггерим событие change для обновления корзины
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    // Для jQuery
                    if (typeof jQuery !== 'undefined') {
                        jQuery(input).trigger('change');
                    }
                }
            });

            // Обработчик для кнопки плюс
            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let currentValue = parseFloat(input.value) || min;
                let newValue = currentValue + step;

                if (newValue <= max) {
                    input.value = newValue;
                    // Триггерим событие change для обновления корзины
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    // Для jQuery
                    if (typeof jQuery !== 'undefined') {
                        jQuery(input).trigger('change');
                    }
                }
            });
        });
    }

    // Инициализация при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuantityButtons);
    } else {
        initQuantityButtons();
    }

    // Реинициализация после обновления корзины (WooCommerce AJAX)
    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('updated_cart_totals updated_checkout', function() {
            setTimeout(initQuantityButtons, 100);
        });
    }

    // Экспортируем функцию для использования извне
    window.initQuantityButtons = initQuantityButtons;
})();
