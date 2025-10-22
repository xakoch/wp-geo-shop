<?php
/**
 * Custom Shop Theme Functions
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

// ==================== THEME SETUP ====================
function customshop_setup() {
    // Поддержка title-tag
    add_theme_support('title-tag');

    // Миниатюры постов
    add_theme_support('post-thumbnails');

    // HTML5 разметка
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));

    // Логотип сайта
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Регистрация меню
    register_nav_menus(array(
        'primary' => 'Основное меню',
        'mobile'  => 'Мобильное меню',
        'footer'  => 'Меню в футере',
    ));
}
add_action('after_setup_theme', 'customshop_setup');

// ==================== WOOCOMMERCE SUPPORT ====================
function customshop_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'customshop_woocommerce_setup');

// ==================== STYLES & SCRIPTS ====================
function customshop_scripts() {
    // Подключаем новые стили из assets
    wp_enqueue_style('customshop-main', get_template_directory_uri() . '/assets/css/style.min.css', array(), '1.0.0');

    // jQuery (уже включен в WordPress)
    wp_enqueue_script('jquery');

    // Подключаем новые скрипты из assets
    wp_enqueue_script('customshop-lib', get_template_directory_uri() . '/assets/js/lib.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('customshop-app', get_template_directory_uri() . '/assets/js/app.min.js', array('jquery', 'customshop-lib'), '1.0.0', true);
    wp_enqueue_script('customshop-add', get_template_directory_uri() . '/assets/js/add.js', array('jquery', 'customshop-app'), '1.0.0', true);

    // Локализация скриптов для AJAX
    wp_localize_script('customshop-add', 'wc_add_to_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
    ));
}
add_action('wp_enqueue_scripts', 'customshop_scripts');

/**
 * Добавляем inline CSS для кнопки add-to-cart, модального окна и мини-корзины
 */
add_action('wp_head', 'customshop_inline_styles');
function customshop_inline_styles() {
    ?>
    <style>
        /* Add to Cart Button Styles */
        .product .add-to-cart.active,
        .product .add-to-cart.added {
            background-color: #176DAA !important;
        }
        .product .add-to-cart.active svg path,
        .product .add-to-cart.added svg path,
        .product .add-to-cart.active img,
        .product .add-to-cart.added img {
            filter: brightness(0) invert(1);
        }

        /* Variation Modal Styles - Sidebar Style like Mini Cart */
        .variation-modal {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 9999 !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
            pointer-events: none;
        }
        .variation-modal.active {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: all !important;
        }
        .variation-modal.hidden {
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .variation-modal.active.hidden {
            visibility: visible !important;
        }
        .variation-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }
        .variation-modal-panel {
            position: absolute !important;
            top: 0 !important;
            right: -100% !important;
            width: 100% !important;
            max-width: 450px !important;
            height: 100% !important;
            background: #fff !important;
            overflow-y: auto !important;
            transition: right 0.3s ease !important;
            z-index: 10000 !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .variation-modal.active .variation-modal-panel {
            right: 0 !important;
        }
        .variation-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            flex-shrink: 0;
        }
        .variation-modal-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        .variation-modal-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .variation-modal-close:hover {
            opacity: 0.7;
        }
        .variation-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .variation-modal-product-info {
            display: flex;
            gap: 15px;
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .variation-modal-image {
            width: 80px;
            flex-shrink: 0;
        }
        .variation-modal-image img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .variation-modal-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .variation-modal-product-title {
            font-size: 16px;
            font-weight: 500;
            margin: 0;
            color: #333;
        }
        .variation-modal-price {
            font-size: 18px;
            font-weight: 600;
            color: #176DAA;
        }
        .variation-modal-options {
            margin-bottom: 20px;
        }
        .variation-attribute {
            margin-bottom: 20px;
        }
        .variation-attribute-label {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 10px 0 !important;
            color: #333;
        }
        .variation-attribute-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .variation-option {
            padding: 8px 16px;
            border: 2px solid #ddd;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }
        .variation-option:hover {
            border-color: #176DAA;
        }
        .variation-option.selected {
            background: #176DAA;
            border-color: #176DAA;
            color: #fff;
        }
        .variation-modal-actions {
            padding: 20px;
            border-top: 1px solid #eee;
            background: #f9f9f9;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .variation-add-to-cart {
            width: 100%;
            padding: 15px;
            background: #176DAA;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .variation-add-to-cart:hover:not(:disabled) {
            opacity: 0.9;
        }
        .variation-add-to-cart:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .variation-add-to-cart.loading {
            opacity: 0.7;
        }
        .open-additional-products-from-variation {
            width: 100%;
            padding: 15px;
            background: #fff;
            color: #176DAA;
            border: 2px solid #176DAA;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .open-additional-products-from-variation:hover {
            background: #f5f5f5;
        }

        /* Additional Products Modal Styles */
        .additional-products-modal {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 9999 !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
            pointer-events: none;
        }
        .additional-products-modal.active {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: all !important;
        }
        .additional-products-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }
        .additional-products-panel {
            position: absolute !important;
            top: 0 !important;
            right: -100% !important;
            width: 100% !important;
            max-width: 450px !important;
            height: 100% !important;
            background: #fff !important;
            overflow-y: auto !important;
            transition: right 0.3s ease !important;
            z-index: 10000 !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .additional-products-modal.active .additional-products-panel {
            right: 0 !important;
        }
        .additional-products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            flex-shrink: 0;
        }
        .additional-products-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        .additional-products-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .additional-products-close:hover {
            opacity: 0.7;
        }
        .additional-products-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .additional-products-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .additional-product-item {
            display: flex !important;
            gap: 15px !important;
            padding: 15px 0 !important;
            border-bottom: 1px solid #eee !important;
            list-style: none !important;
        }
        .additional-product-item:last-child {
            border-bottom: none !important;
        }
        .additional-product-image {
            width: 80px;
            flex-shrink: 0;
        }
        .additional-product-image img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .additional-product-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .additional-product-name {
            font-size: 14px;
            font-weight: 500;
            margin: 0;
        }
        .additional-product-name a {
            color: #333;
            text-decoration: none;
        }
        .additional-product-name a:hover {
            color: #176DAA;
        }
        .additional-product-price {
            font-size: 16px;
            font-weight: 600;
            color: #176DAA;
        }
        .additional-product-variations {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 8px;
        }
        .variation-select-wrapper {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .variation-select-wrapper label {
            font-size: 12px;
            font-weight: 500;
            color: #666;
        }
        .variation-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            color: #333;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .variation-select:hover {
            border-color: #176DAA;
        }
        .variation-select:focus {
            outline: none;
            border-color: #176DAA;
        }
        .additional-product-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px;
        }
        .additional-product-add-to-cart {
            padding: 8px 16px;
            background: #f5f5f5;
            color: #176DAA;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .additional-product-add-to-cart:hover {
            background: #e8e8e8;
        }
        .additional-product-add-to-cart.added {
            background: #176DAA !important;
            color: #fff !important;
        }
        .additional-product-add-to-cart.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        .additional-product-add-to-cart:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        .additional-product-add-to-cart .checkmark-icon {
            display: none;
            width: 14px;
            height: 14px;
        }
        .additional-product-add-to-cart.added .checkmark-icon {
            display: inline-block;
        }
        .additional-product-remove {
            display: none;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            background: none;
            border: none;
            color: #F52222;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        .additional-product-remove:hover {
            opacity: 0.7;
        }
        .additional-product-item.in-cart .additional-product-remove {
            display: flex;
        }
    </style>
    <?php
}


// ==================== SIDEBAR ====================
function customshop_widgets_init() {
    register_sidebar(array(
        'name'          => 'Основной сайдбар',
        'id'            => 'sidebar-1',
        'description'   => 'Добавьте виджеты в сайдбар',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'customshop_widgets_init');


// ==================== WOOCOMMERCE AJAX & MINI CART ====================

/**
 * Обеспечиваем, что AJAX добавление в корзину включено на страницах архивов
 */
function customshop_ensure_ajax_add_to_cart_is_enabled() {
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        wp_enqueue_script('wc-add-to-cart');
    }
}
add_action('wp_enqueue_scripts', 'customshop_ensure_ajax_add_to_cart_is_enabled');

/**
 * Включаем AJAX добавление в корзину для простых товаров на странице товара
 */
add_filter('woocommerce_product_single_add_to_cart_text', 'customshop_single_product_ajax_add_to_cart');
function customshop_single_product_ajax_add_to_cart($text) {
    global $product;

    // Проверяем, что объект продукта существует
    if (!$product || !is_object($product)) {
        return $text;
    }

    return __('Добавить в корзину', 'woocommerce');
}

/**
 * Добавляем класс для AJAX кнопки на странице товара
 */
add_filter('woocommerce_loop_add_to_cart_link', 'customshop_add_ajax_class_to_single_product', 10, 3);
function customshop_add_ajax_class_to_single_product($html, $product, $args) {
    // Проверяем, что объект продукта существует
    if (!$product || !is_object($product) || !method_exists($product, 'is_type')) {
        return $html;
    }

    // Если это простой товар, добавляем AJAX класс
    if ($product->is_type('simple')) {
        $html = str_replace('add_to_cart_button', 'add_to_cart_button ajax_add_to_cart', $html);
    }
    return $html;
}

/**
 * AJAX обработчик для добавления в корзину со страницы товара
 */
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'customshop_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'customshop_ajax_add_to_cart');

function customshop_ajax_add_to_cart() {
    // Проверяем, что это AJAX запрос и данные существуют
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Missing product_id');
        wp_die();
    }

    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX::get_refreshed_fragments();
    } else {
        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
        );

        wp_send_json($data);
    }

    wp_die();
}


/**
 * Обновление фрагментов корзины (для AJAX)
 */
add_filter('woocommerce_add_to_cart_fragments', 'customshop_mini_cart_fragments');

function customshop_mini_cart_fragments($fragments) {
    // Обновление счётчика в header
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();

    // Обновление тела мини-корзины
    ob_start();
    woocommerce_mini_cart();
    $fragments['.mini-cart-body'] = '<div class="mini-cart-body">' . ob_get_clean() . '</div>';

    return $fragments;
}


/**
 * JS для управления мини-корзиной и AJAX
 */
add_action('wp_footer', 'customshop_mini_cart_js');

function customshop_mini_cart_js() {
    // Проверяем, что WooCommerce активен
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Не загружаем на страницах корзины и оформления заказа
    if (is_cart() || is_checkout()) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(function($) {

        if (typeof wc_add_to_cart_params === 'undefined') {
            wc_add_to_cart_params = {
                ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'
            };
        }

        // ========== MINI CART TOGGLE ==========
        var $miniCart = $('.mini-cart-sidebar');
        var $miniCartOverlay = $('.mini-cart-overlay');
        var $miniCartClose = $('.mini-cart-close');
        var $cartLink = $('.cart-link');

        function openMiniCart() {
            $miniCart.addClass('active');
            $('body').addClass('mini-cart-open');
        }

        function closeMiniCart() {
            $miniCart.removeClass('active');
            $('body').removeClass('mini-cart-open');
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
            // Проверяем, не из additional products ли это добавление
            var isFromAdditionalProducts = $button && $button.hasClass('additional-product-add-to-cart');

            if (!isFromAdditionalProducts) {
                showNotification($button.closest('.product').find('.woocommerce-loop-product__title').text() || 'Товар');

                // Открываем мини-корзину с небольшой задержкой
                setTimeout(function() {
                    openMiniCart();
                }, 300);
            }

            // Анимация счётчика (всегда)
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
        }

        function closeAccountSidebar() {
            $accountSidebar.removeClass('active');
            $('body').removeClass('account-open');
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

        // ========== ADD TO CART FROM CATALOG ==========
        $(document).on('click', '.product .add-to-cart:not(.open-variation-modal)', function(e) {
            e.preventDefault();

            var $button = $(this);
            var productId = $button.data('product-id');

            if (!productId || $button.hasClass('loading')) {
                return;
            }

            // Добавляем класс загрузки
            $button.addClass('loading');

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: productId,
                    quantity: 1
                },
                success: function(response) {
                    if (response.error) {
                        // Ошибка
                        $button.removeClass('loading');
                        return;
                    }

                    // Успешно добавлено
                    $button.removeClass('loading').addClass('added active');

                    // Меняем иконку на checkmark
                    $button.html('<img src="<?php echo get_template_directory_uri(); ?>/assets/img/checkmark.svg" alt="Added">');

                    // Обновляем фрагменты корзины через встроенный механизм WooCommerce
                    if (response.fragments) {
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    // Trigger события для обновления корзины
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);

                    // Открываем мини-корзину
                    setTimeout(function() {
                        openMiniCart();
                    }, 500);

                    // Возвращаем исходную иконку через 2 секунды
                    setTimeout(function() {
                        $button.removeClass('added active');
                        $button.html('<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.8574 0V9.14258H20V10.8574H10.8574V20H9.14258V10.8574H0V9.14258H9.14258V0H10.8574Z" fill="#176DAA"/></svg>');
                    }, 2000);
                },
                error: function() {
                    $button.removeClass('loading');
                }
            });
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
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
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
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
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

        // ========== VARIATION MODAL ==========
        var $variationModal = $('.variation-modal');
        var $variationModalOverlay = $('.variation-modal-overlay');
        var $variationModalClose = $('.variation-modal-close');
        var selectedVariation = null;

        function openVariationModal() {
            $variationModal.addClass('active');
            $('body').addClass('variation-modal-open');
        }

        function closeVariationModal() {
            $variationModal.removeClass('active');
            $('body').removeClass('variation-modal-open');
            selectedVariation = null;
            $('.variation-add-to-cart').prop('disabled', true);
        }

        $variationModalClose.on('click', closeVariationModal);
        $variationModalOverlay.on('click', closeVariationModal);

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $variationModal.hasClass('active')) {
                closeVariationModal();
            }
        });

        // Open modal when clicking on variable product button
        $(document).on('click', '.open-variation-modal', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $button = $(this);
            var productId = $button.data('product-id');

            console.log('Variation modal button clicked', productId);

            if (!productId || $button.hasClass('loading')) {
                console.log('No product ID or already loading');
                return;
            }

            // Load product variations
            $button.addClass('loading');

            console.log('Loading variations for product:', productId);

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_product_variations',
                    product_id: productId
                },
                success: function(response) {
                    console.log('Variations response:', response);
                    $button.removeClass('loading');

                    if (response.success && response.data) {
                        // Check if product has variations
                        if (!response.data.variations || response.data.variations.length === 0) {
                            console.log('No variations found for this product');
                            showNotification('This product has no variations', true);
                            return;
                        }

                        // Populate modal with product info
                        $('.variation-modal-image').html('<img src="' + response.data.image + '" alt="' + response.data.title + '">');
                        $('.variation-modal-product-title').text(response.data.title);
                        $('.variation-modal-price').html(response.data.price);

                        // Populate variations
                        var optionsHtml = '';

                        if (response.data.attributes) {
                            $.each(response.data.attributes, function(attrName, attrData) {
                                optionsHtml += '<div class="variation-attribute">';
                                optionsHtml += '<h5 class="variation-attribute-label">' + attrData.label + '</h5>';
                                optionsHtml += '<div class="variation-attribute-options">';

                                $.each(attrData.options, function(index, option) {
                                    optionsHtml += '<button class="variation-option" data-attribute="' + attrName + '" data-value="' + option + '">' + option + '</button>';
                                });

                                optionsHtml += '</div>';
                                optionsHtml += '</div>';
                            });
                        }

                        $('.variation-modal-options').html(optionsHtml);

                        // Store variations data
                        $variationModal.data('variations', response.data.variations);
                        $variationModal.data('product-id', productId);

                        // Show/hide Additional Products button based on availability
                        if (response.data.has_additional_products) {
                            $('.open-additional-products-from-variation').show();
                        } else {
                            $('.open-additional-products-from-variation').hide();
                        }

                        // Open modal
                        openVariationModal();
                    } else {
                        console.log('Error loading variations:', response);
                        showNotification('Error loading product variations', true);
                    }
                },
                error: function() {
                    $button.removeClass('loading');
                    showNotification('Ошибка загрузки вариаций товара', true);
                }
            });
        });

        // Handle variation option selection
        $(document).on('click', '.variation-option', function() {
            var $option = $(this);
            var attribute = $option.data('attribute');

            // Toggle selection in the same attribute group
            $option.closest('.variation-attribute-options').find('.variation-option').removeClass('selected');
            $option.addClass('selected');

            // Check if all attributes are selected
            var allSelected = true;
            var selectedAttributes = {};

            $('.variation-attribute').each(function() {
                var $selected = $(this).find('.variation-option.selected');
                if ($selected.length === 0) {
                    allSelected = false;
                } else {
                    selectedAttributes[$selected.data('attribute')] = $selected.data('value');
                }
            });

            // Find matching variation
            if (allSelected) {
                var variations = $variationModal.data('variations');
                var matchedVariation = null;

                console.log('Selected attributes:', selectedAttributes);
                console.log('Available variations:', variations);

                $.each(variations, function(index, variation) {
                    var match = true;
                    $.each(selectedAttributes, function(attr, value) {
                        // Compare values in lowercase for better matching
                        var variationValue = variation.attributes[attr] ? variation.attributes[attr].toLowerCase() : '';
                        var selectedValue = value.toLowerCase();

                        if (variationValue !== selectedValue) {
                            match = false;
                            return false;
                        }
                    });

                    if (match) {
                        matchedVariation = variation;
                        return false;
                    }
                });

                console.log('Matched variation:', matchedVariation);

                if (matchedVariation) {
                    selectedVariation = matchedVariation;
                    $('.variation-modal-price').html(matchedVariation.price_html);
                    $('.variation-add-to-cart').prop('disabled', false);
                } else {
                    selectedVariation = null;
                    $('.variation-add-to-cart').prop('disabled', true);
                }
            } else {
                $('.variation-add-to-cart').prop('disabled', true);
            }
        });

        // Add variation to cart
        $(document).on('click', '.variation-add-to-cart', function(e) {
            e.preventDefault();

            if (!selectedVariation) {
                return;
            }

            var $button = $(this);
            var productId = $variationModal.data('product-id');

            $button.addClass('loading').prop('disabled', true);

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: productId,
                    variation_id: selectedVariation.variation_id,
                    quantity: 1
                },
                success: function(response) {
                    $button.removeClass('loading').addClass('added');

                    if (response.error) {
                        showNotification('Ошибка добавления в корзину', true);
                        $button.prop('disabled', false).removeClass('added');
                        return;
                    }

                    // Update cart fragments
                    if (response.fragments) {
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);

                    // Close modal
                    setTimeout(function() {
                        closeVariationModal();
                        openMiniCart();
                    }, 500);
                },
                error: function() {
                    $button.removeClass('loading').prop('disabled', false);
                    showNotification('Ошибка добавления в корзину', true);
                }
            });
        });

        // ========== ADDITIONAL PRODUCTS MODAL ==========
        var $additionalProductsModal = $('.additional-products-modal');
        var $additionalProductsOverlay = $('.additional-products-overlay');
        var $additionalProductsClose = $('.additional-products-close');

        function openAdditionalProductsModal() {
            $additionalProductsModal.addClass('active');
            $('body').addClass('additional-products-modal-open');
        }

        function closeAdditionalProductsModal(returnToVariation) {
            $additionalProductsModal.removeClass('active');
            $('body').removeClass('additional-products-modal-open');

            // If we came from variation modal, return to it
            if (returnToVariation && $additionalProductsModal.data('from-variation')) {
                setTimeout(function() {
                    $variationModal.removeClass('hidden');
                    // Modal is already open, just make it visible
                }, 300);

                // Clear the marker
                $additionalProductsModal.data('from-variation', false);
            }
        }

        $additionalProductsClose.on('click', function() {
            closeAdditionalProductsModal(true);
        });

        $additionalProductsOverlay.on('click', function() {
            closeAdditionalProductsModal(true);
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $additionalProductsModal.hasClass('active')) {
                closeAdditionalProductsModal(true);
            }
        });

        // Open additional products modal from variation modal
        $(document).on('click', '.open-additional-products-from-variation', function(e) {
            e.preventDefault();

            var $button = $(this);
            var productId = $variationModal.data('product-id') || 0;

            console.log('Opening additional products for product:', productId);

            $button.addClass('loading');

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_additional_products',
                    product_id: productId
                },
                success: function(response) {
                    $button.removeClass('loading');

                    if (response.success && response.data && response.data.products) {
                        if (response.data.products.length === 0) {
                            showNotification('No additional products available', true);
                            return;
                        }

                        var productsHtml = '';

                        $.each(response.data.products, function(index, product) {
                            var inCartClass = product.in_cart ? ' in-cart' : '';
                            var buttonText = product.in_cart ? 'Added' : 'Add to Cart';
                            var buttonClass = product.in_cart ? 'added' : '';
                            var isVariable = product.is_variable && product.variations && product.variations.length > 0;

                            productsHtml += '<li class="additional-product-item' + inCartClass + '" data-product-id="' + product.id + '" data-is-variable="' + (isVariable ? 'true' : 'false') + '">';
                            productsHtml += '  <div class="additional-product-image"><img src="' + product.image + '" alt="' + product.title + '"></div>';
                            productsHtml += '  <div class="additional-product-details">';
                            productsHtml += '    <h4 class="additional-product-name"><a href="' + product.url + '">' + product.title + '</a></h4>';
                            productsHtml += '    <div class="additional-product-price">' + product.price + '</div>';

                            // Add variation selects if product is variable
                            if (isVariable) {
                                productsHtml += '    <div class="additional-product-variations">';

                                // Get all unique attributes
                                var attributes = {};
                                $.each(product.variations, function(i, variation) {
                                    $.each(variation.attributes, function(attrName, attrData) {
                                        if (!attributes[attrName]) {
                                            attributes[attrName] = [];
                                        }
                                        var exists = false;
                                        $.each(attributes[attrName], function(j, existingValue) {
                                            if (existingValue.slug === attrData.slug) {
                                                exists = true;
                                                return false;
                                            }
                                        });
                                        if (!exists) {
                                            attributes[attrName].push(attrData);
                                        }
                                    });
                                });

                                // Create select for each attribute
                                $.each(attributes, function(attrName, values) {
                                    productsHtml += '      <div class="variation-select-wrapper">';
                                    productsHtml += '        <label>' + attrName + '</label>';
                                    productsHtml += '        <select class="variation-select" data-attribute="' + attrName + '">';
                                    productsHtml += '          <option value="">Choose ' + attrName + '</option>';
                                    $.each(values, function(i, valueData) {
                                        productsHtml += '          <option value="' + valueData.slug + '">' + valueData.value + '</option>';
                                    });
                                    productsHtml += '        </select>';
                                    productsHtml += '      </div>';
                                });

                                // Store variations data
                                productsHtml += '      <input type="hidden" class="variations-data" value=\'' + JSON.stringify(product.variations) + '\' />';
                                productsHtml += '    </div>';
                            }

                            productsHtml += '    <div class="additional-product-actions">';
                            productsHtml += '      <button class="additional-product-add-to-cart ' + buttonClass + '" data-product-id="' + product.id + '" ' + (isVariable && !product.in_cart ? 'disabled' : '') + '>';
                            productsHtml += '        <svg class="checkmark-icon" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                            productsHtml += '        <span>' + buttonText + '</span>';
                            productsHtml += '      </button>';
                            productsHtml += '      <button class="additional-product-remove" data-product-id="' + product.id + '"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14 4H13V14C13 14.2652 12.8946 14.5195 12.707 14.707C12.5195 14.8946 12.2652 15 12 15H4C3.73478 15 3.48051 14.8946 3.29297 14.707C3.10543 14.5195 3 14.2652 3 14V4H2V3H14V4ZM4 14H12V4H4V14ZM7 12H6V6H7V12ZM10 12H9V6H10V12ZM10 1V2H6V1H10Z" fill="#F52222"/></svg><span>Remove</span></button>';
                            productsHtml += '    </div></div></li>';
                        });

                        $('.additional-products-list').html(productsHtml);

                        // Mark that we came from variation modal (don't close it)
                        $additionalProductsModal.data('from-variation', true);

                        // Just hide variation modal (don't close it)
                        $variationModal.addClass('hidden');

                        // Open additional products modal
                        openAdditionalProductsModal();
                    } else {
                        showNotification('No additional products found', true);
                    }
                },
                error: function() {
                    $button.removeClass('loading');
                    showNotification('Error loading additional products', true);
                }
            });
        });

        // Open additional products modal (general)
        $(document).on('click', '.open-additional-products', function(e) {
            e.preventDefault();

            var $button = $(this);
            var productId = $button.data('product-id') || 0;

            $button.addClass('loading');

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_additional_products',
                    product_id: productId
                },
                success: function(response) {
                    $button.removeClass('loading');

                    if (response.success && response.data && response.data.products) {
                        var productsHtml = '';

                        $.each(response.data.products, function(index, product) {
                            var inCartClass = product.in_cart ? ' in-cart' : '';
                            var buttonText = product.in_cart ? 'Added' : 'Add to Cart';
                            var buttonClass = product.in_cart ? 'added' : '';
                            var isVariable = product.is_variable && product.variations && product.variations.length > 0;

                            productsHtml += '<li class="additional-product-item' + inCartClass + '" data-product-id="' + product.id + '" data-is-variable="' + (isVariable ? 'true' : 'false') + '">';
                            productsHtml += '  <div class="additional-product-image"><img src="' + product.image + '" alt="' + product.title + '"></div>';
                            productsHtml += '  <div class="additional-product-details">';
                            productsHtml += '    <h4 class="additional-product-name"><a href="' + product.url + '">' + product.title + '</a></h4>';
                            productsHtml += '    <div class="additional-product-price">' + product.price + '</div>';

                            // Add variation selects if product is variable
                            if (isVariable) {
                                productsHtml += '    <div class="additional-product-variations">';

                                // Get all unique attributes
                                var attributes = {};
                                $.each(product.variations, function(i, variation) {
                                    $.each(variation.attributes, function(attrName, attrData) {
                                        if (!attributes[attrName]) {
                                            attributes[attrName] = [];
                                        }
                                        var exists = false;
                                        $.each(attributes[attrName], function(j, existingValue) {
                                            if (existingValue.slug === attrData.slug) {
                                                exists = true;
                                                return false;
                                            }
                                        });
                                        if (!exists) {
                                            attributes[attrName].push(attrData);
                                        }
                                    });
                                });

                                // Create select for each attribute
                                $.each(attributes, function(attrName, values) {
                                    productsHtml += '      <div class="variation-select-wrapper">';
                                    productsHtml += '        <label>' + attrName + '</label>';
                                    productsHtml += '        <select class="variation-select" data-attribute="' + attrName + '">';
                                    productsHtml += '          <option value="">Choose ' + attrName + '</option>';
                                    $.each(values, function(i, valueData) {
                                        productsHtml += '          <option value="' + valueData.slug + '">' + valueData.value + '</option>';
                                    });
                                    productsHtml += '        </select>';
                                    productsHtml += '      </div>';
                                });

                                // Store variations data
                                productsHtml += '      <input type="hidden" class="variations-data" value=\'' + JSON.stringify(product.variations) + '\' />';
                                productsHtml += '    </div>';
                            }

                            productsHtml += '    <div class="additional-product-actions">';
                            productsHtml += '      <button class="additional-product-add-to-cart ' + buttonClass + '" data-product-id="' + product.id + '" ' + (isVariable && !product.in_cart ? 'disabled' : '') + '>';
                            productsHtml += '        <svg class="checkmark-icon" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                            productsHtml += '        <span>' + buttonText + '</span>';
                            productsHtml += '      </button>';
                            productsHtml += '      <button class="additional-product-remove" data-product-id="' + product.id + '"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M14 4H13V14C13 14.2652 12.8946 14.5195 12.707 14.707C12.5195 14.8946 12.2652 15 12 15H4C3.73478 15 3.48051 14.8946 3.29297 14.707C3.10543 14.5195 3 14.2652 3 14V4H2V3H14V4ZM4 14H12V4H4V14ZM7 12H6V6H7V12ZM10 12H9V6H10V12ZM10 1V2H6V1H10Z" fill="#F52222"/></svg><span>Remove</span></button>';
                            productsHtml += '    </div></div></li>';
                        });

                        $('.additional-products-list').html(productsHtml);
                        openAdditionalProductsModal();
                    }
                },
                error: function() {
                    $button.removeClass('loading');
                }
            });
        });

        // Handle variation selection change in additional products
        $(document).on('change', '.additional-product-item .variation-select', function() {
            var $productItem = $(this).closest('.additional-product-item');
            var $addButton = $productItem.find('.additional-product-add-to-cart');
            var variationsData = JSON.parse($productItem.find('.variations-data').val());

            // Get all selected attributes
            var selectedAttributes = {};
            var allSelected = true;

            $productItem.find('.variation-select').each(function() {
                var attrName = $(this).data('attribute');
                var attrValue = $(this).val();

                if (attrValue) {
                    selectedAttributes[attrName] = attrValue;
                } else {
                    allSelected = false;
                }
            });

            // Find matching variation
            if (allSelected) {
                var matchedVariation = null;

                $.each(variationsData, function(i, variation) {
                    var matches = true;

                    $.each(selectedAttributes, function(attrName, attrValue) {
                        if (!variation.attributes[attrName] || variation.attributes[attrName].slug !== attrValue) {
                            matches = false;
                            return false;
                        }
                    });

                    if (matches) {
                        matchedVariation = variation;
                        return false;
                    }
                });

                if (matchedVariation) {
                    // Enable button and store variation_id
                    $addButton.prop('disabled', false);
                    $addButton.data('variation-id', matchedVariation.variation_id);

                    // Update price if needed
                    $productItem.find('.additional-product-price').html(matchedVariation.price);
                } else {
                    $addButton.prop('disabled', true);
                    $addButton.removeData('variation-id');
                }
            } else {
                $addButton.prop('disabled', true);
                $addButton.removeData('variation-id');
            }
        });

        // Add to cart from additional products
        $(document).on('click', '.additional-product-add-to-cart:not(.added)', function(e) {
            e.preventDefault();
            var $button = $(this);
            var productId = $button.data('product-id');
            var variationId = $button.data('variation-id');
            var $productItem = $button.closest('.additional-product-item');
            var isVariable = $productItem.data('is-variable') === 'true' || $productItem.data('is-variable') === true;

            if ($button.hasClass('loading') || $button.prop('disabled')) return;

            // For variable products, check if variation is selected
            if (isVariable && !variationId) {
                showNotification('Please select all options', true);
                return;
            }

            $button.addClass('loading');

            var ajaxData = {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: isVariable ? variationId : productId,
                quantity: 1
            };

            // Add variation data if needed
            if (isVariable && variationId) {
                ajaxData.variation_id = variationId;
            }

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    $button.removeClass('loading').addClass('added');
                    $button.find('span').text('Added');
                    $productItem.addClass('in-cart');

                    if (response.fragments) {
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                },
                error: function() {
                    $button.removeClass('loading');
                }
            });
        });

        // Remove from cart (additional products)
        $(document).on('click', '.additional-product-remove', function(e) {
            e.preventDefault();
            var $button = $(this);
            var productId = $button.data('product-id');
            var $productItem = $button.closest('.additional-product-item');
            var $addButton = $productItem.find('.additional-product-add-to-cart');

            console.log('Remove clicked, productId:', productId);

            if ($button.hasClass('loading')) return;

            $button.addClass('loading');

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'remove_cart_item',
                    product_id: productId
                },
                success: function(response) {
                    console.log('Remove response:', response);
                    $button.removeClass('loading');

                    if (response.success) {
                        // Update button states
                        $addButton.removeClass('added');
                        $addButton.find('span').text('Add to Cart');
                        $productItem.removeClass('in-cart');
                        $addButton.prop('disabled', false);

                        // Update cart fragments
                        if (response.data && response.data.fragments) {
                            $.each(response.data.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        $(document.body).trigger('removed_from_cart', [response.data.fragments, response.data.cart_hash]);
                    } else {
                        console.error('Remove failed:', response);
                        showNotification('Error removing from cart', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Remove AJAX error:', xhr, status, error);
                    $button.removeClass('loading');
                    showNotification('Error removing from cart', true);
                }
            });
        });
    });
    </script>
    <?php
}

// ==================== AJAX LOGIN & REGISTER ====================

/**
 * AJAX Login
 */
add_action('wp_ajax_nopriv_ajax_login', 'customshop_ajax_login');
function customshop_ajax_login() {
    // Проверяем nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'ajax-login-nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу и попробуйте снова.');
        return;
    }

    // Получаем данные
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['rememberme']) ? true : false;

    // Валидация
    if (empty($username) || empty($password)) {
        wp_send_json_error('Заполните все обязательные поля.');
        return;
    }

    // Данные для входа
    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember
    );

    // Попытка входа
    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        // Переводим стандартные сообщения об ошибках
        $error_message = $user->get_error_message();

        if (strpos($error_message, 'incorrect') !== false || strpos($error_message, 'Invalid') !== false) {
            $error_message = 'Неверный логин или пароль.';
        }

        wp_send_json_error($error_message);
        return;
    }

    wp_send_json_success(array('message' => 'Вы успешно вошли в систему!'));
}

/**
 * AJAX Register
 */
add_action('wp_ajax_nopriv_ajax_register', 'customshop_ajax_register');
function customshop_ajax_register() {
    // Проверяем nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'ajax-register-nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу и попробуйте снова.');
        return;
    }

    // Проверяем, что регистрация включена
    if (!get_option('users_can_register')) {
        wp_send_json_error('Регистрация новых пользователей отключена.');
        return;
    }

    // Получаем и очищаем данные
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Валидация
    if (empty($username) || empty($email) || empty($password)) {
        wp_send_json_error('Заполните все обязательные поля.');
        return;
    }

    if (strlen($username) < 3) {
        wp_send_json_error('Логин должен содержать минимум 3 символа.');
        return;
    }

    if (strlen($password) < 6) {
        wp_send_json_error('Пароль должен содержать минимум 6 символов.');
        return;
    }

    if (!is_email($email)) {
        wp_send_json_error('Неверный формат email.');
        return;
    }

    if (username_exists($username)) {
        wp_send_json_error('Пользователь с таким логином уже существует.');
        return;
    }

    if (email_exists($email)) {
        wp_send_json_error('Пользователь с таким email уже зарегистрирован.');
        return;
    }

    // Создаём пользователя
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error('Ошибка регистрации: ' . $user_id->get_error_message());
        return;
    }

    // Автоматический вход после регистрации
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    // Отправляем приветственное письмо (опционально)
    wp_new_user_notification($user_id, null, 'user');

    wp_send_json_success(array('message' => 'Регистрация прошла успешно! Вы автоматически вошли в систему.'));
}

// ==================== AJAX GET PRODUCT VARIATIONS ====================

/**
 * AJAX handler to get product variations
 */
add_action('wp_ajax_get_product_variations', 'customshop_ajax_get_product_variations');
add_action('wp_ajax_nopriv_get_product_variations', 'customshop_ajax_get_product_variations');

function customshop_ajax_get_product_variations() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Missing product_id');
        wp_die();
    }

    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid product');
        wp_die();
    }

    // Get product image
    $image_id = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_single') : wc_placeholder_img_src();

    // Get product attributes
    $attributes = array();
    $available_variations = $product->get_available_variations();

    foreach ($product->get_variation_attributes() as $attribute_name => $options) {
        $attribute_label = wc_attribute_label($attribute_name);

        // Normalize attribute name for JavaScript
        $normalized_name = 'attribute_' . sanitize_title($attribute_name);

        $attributes[$normalized_name] = array(
            'label' => $attribute_label,
            'options' => $options
        );
    }

    // Format variations
    $variations = array();
    foreach ($available_variations as $variation) {
        $variation_obj = wc_get_product($variation['variation_id']);

        // Normalize attribute keys in variation
        $normalized_attributes = array();
        foreach ($variation['attributes'] as $key => $value) {
            // Key already comes with 'attribute_' prefix from WooCommerce
            $normalized_attributes[$key] = $value;
        }

        $variations[] = array(
            'variation_id' => $variation['variation_id'],
            'attributes' => $normalized_attributes,
            'price_html' => $variation_obj->get_price_html(),
            'is_in_stock' => $variation['is_in_stock'],
            'image' => isset($variation['image']['url']) ? $variation['image']['url'] : $image_url
        );
    }

    // Check if product has additional products (upsells, cross-sells, or related)
    $has_additional = false;
    $upsell_ids = $product->get_upsell_ids();
    $crosssell_ids = $product->get_cross_sell_ids();

    if (!empty($upsell_ids) || !empty($crosssell_ids)) {
        $has_additional = true;
    } else {
        // Check if there are related products
        $related_ids = wc_get_related_products($product_id, 5);
        if (!empty($related_ids)) {
            $has_additional = true;
        }
    }

    wp_send_json_success(array(
        'title' => $product->get_name(),
        'image' => $image_url,
        'price' => $product->get_price_html(),
        'attributes' => $attributes,
        'variations' => $variations,
        'has_additional_products' => $has_additional
    ));

    wp_die();
}

// ==================== AJAX GET ADDITIONAL PRODUCTS ====================

/**
 * AJAX handler to get additional/related products
 */
add_action('wp_ajax_get_additional_products', 'customshop_ajax_get_additional_products');
add_action('wp_ajax_nopriv_get_additional_products', 'customshop_ajax_get_additional_products');

function customshop_ajax_get_additional_products() {
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

    $products_data = array();
    $cart_contents = WC()->cart->get_cart();
    $cart_product_ids = array();

    // Get IDs of products in cart
    foreach ($cart_contents as $cart_item) {
        $cart_product_ids[] = $cart_item['product_id'];
    }

    if ($product_id > 0) {
        // Get upsells, cross-sells and related products for specific product
        $product = wc_get_product($product_id);
        if ($product) {
            $product_ids = array();

            // First priority: Upsells (set in admin)
            $upsell_ids = $product->get_upsell_ids();
            if (!empty($upsell_ids)) {
                $product_ids = array_merge($product_ids, $upsell_ids);
            }

            // Second priority: Cross-sells (set in admin)
            $crosssell_ids = $product->get_cross_sell_ids();
            if (!empty($crosssell_ids)) {
                $product_ids = array_merge($product_ids, $crosssell_ids);
            }

            // Third priority: Related products (automatic by category/tags)
            $related_ids = wc_get_related_products($product_id, 15);
            if (!empty($related_ids)) {
                $product_ids = array_merge($product_ids, $related_ids);
            }

            // Remove duplicates and limit to 15
            $product_ids = array_unique($product_ids);
            $product_ids = array_slice($product_ids, 0, 15);
        } else {
            $product_ids = array();
        }
    } else {
        // Get general popular/featured products
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 15,
            'orderby' => 'rand',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);
        $product_ids = wp_list_pluck($query->posts, 'ID');
    }

    foreach ($product_ids as $id) {
        $product = wc_get_product($id);

        if (!$product || !$product->is_visible()) {
            continue;
        }

        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src();

        $product_item = array(
            'id' => $product->get_id(),
            'title' => $product->get_name(),
            'url' => get_permalink($product->get_id()),
            'image' => $image_url,
            'price' => $product->get_price_html(),
            'in_cart' => in_array($product->get_id(), $cart_product_ids),
            'is_variable' => $product->is_type('variable'),
            'variations' => array()
        );

        // If variable product, get variations
        if ($product->is_type('variable')) {
            $available_variations = $product->get_available_variations();

            foreach ($available_variations as $variation) {
                $variation_obj = wc_get_product($variation['variation_id']);
                $attributes = array();

                foreach ($variation['attributes'] as $attr_name => $attr_value) {
                    $taxonomy = str_replace('attribute_', '', $attr_name);
                    $term = get_term_by('slug', $attr_value, $taxonomy);
                    $label_name = wc_attribute_label($taxonomy);

                    $attributes[$label_name] = array(
                        'name' => $label_name,
                        'value' => $term ? $term->name : $attr_value,
                        'slug' => $attr_value
                    );
                }

                $product_item['variations'][] = array(
                    'variation_id' => $variation['variation_id'],
                    'attributes' => $attributes,
                    'price' => $variation_obj->get_price_html(),
                    'is_in_stock' => $variation['is_in_stock']
                );
            }
        }

        $products_data[] = $product_item;
    }

    wp_send_json_success(array(
        'products' => $products_data
    ));

    wp_die();
}

/**
 * AJAX handler to remove item from cart
 */
add_action('wp_ajax_remove_cart_item', 'customshop_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'customshop_ajax_remove_cart_item');

function customshop_ajax_remove_cart_item() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Missing product_id'));
        wp_die();
    }

    $product_id = absint($_POST['product_id']);
    $cart = WC()->cart->get_cart();
    $removed = false;

    foreach ($cart as $cart_item_key => $cart_item) {
        // Check both product_id and variation_id (in case it's a variation)
        if ($cart_item['product_id'] == $product_id ||
            (isset($cart_item['variation_id']) && $cart_item['variation_id'] == $product_id)) {
            WC()->cart->remove_cart_item($cart_item_key);
            $removed = true;
            break;
        }
    }

    if ($removed) {
        WC()->cart->calculate_totals();

        // Get fragments manually
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $data = array(
            'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            )),
            'cart_hash' => WC()->cart->get_cart_hash()
        );

        wp_send_json_success($data);
    } else {
        wp_send_json_error(array('message' => 'Product not found in cart'));
    }

    wp_die();
}

// ==================== ALL CATEGORIES PAGE ====================

/**
 * Создаёт страницу "Все категории" при активации темы
 */
function customshop_create_all_categories_page() {
    // Проверяем, существует ли уже страница
    $page_slug = 'all-categories';
    $page_check = get_page_by_path($page_slug);

    if (!$page_check) {
        // Создаём страницу
        $page_data = array(
            'post_title'     => 'Все категории',
            'post_content'   => '',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_name'      => $page_slug,
            'page_template'  => 'template-all-categories.php',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        );

        wp_insert_post($page_data);
    }
}
add_action('after_switch_theme', 'customshop_create_all_categories_page');

/**
 * Получить URL страницы "Все категории"
 */
function customshop_get_all_categories_page_url() {
    $page = get_page_by_path('all-categories');

    if ($page) {
        return get_permalink($page->ID);
    }

    // Fallback на страницу магазина
    return get_permalink(wc_get_page_id('shop'));
}

// ==================== WOOCOMMERCE CUSTOMIZATION ====================

/**
 * Отключаем стандартные WooCommerce хуки (управляем всем в archive-product.php)
 */
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

// ==================== AJAX SHOP FILTERING ====================

/**
 * AJAX обработчик для фильтрации/сортировки товаров
 */
add_action('wp_ajax_filter_products', 'customshop_ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'customshop_ajax_filter_products');

function customshop_ajax_filter_products() {
    // Устанавливаем query vars из AJAX запроса
    if (isset($_POST['orderby'])) {
        $_GET['orderby'] = sanitize_text_field($_POST['orderby']);
    }

    if (isset($_POST['paged'])) {
        set_query_var('paged', intval($_POST['paged']));
    }

    // Подготавливаем query
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => isset($_POST['paged']) ? intval($_POST['paged']) : 1,
    );

    // Сортировка
    if (isset($_POST['orderby'])) {
        switch ($_POST['orderby']) {
            case 'popularity':
                $args['meta_key'] = 'total_sales';
                $args['orderby']  = 'meta_value_num';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby']  = 'meta_value_num';
                break;
            case 'date':
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            default:
                $args['orderby'] = 'menu_order title';
                break;
        }
    }

    // Категория
    if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_POST['product_cat']),
            ),
        );
    }

    // Query
    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        ?>
        <div class="products">
            <?php
            while ($query->have_posts()) {
                $query->the_post();
                wc_get_template_part('content', 'product');
            }
            ?>
        </div>
        <?php

        // Пагинация
        if ($query->max_num_pages > 1) {
            $current_page = max(1, $query->query_vars['paged']);
            ?>
            <div class="pagination">
                <div class="pagination__numbers">
                    <?php
                    echo paginate_links(array(
                        'base'      => esc_url_raw(str_replace(999999999, '%#%', get_pagenum_link(999999999))),
                        'format'    => '?paged=%#%',
                        'current'   => $current_page,
                        'total'     => $query->max_num_pages,
                        'prev_text' => '',
                        'next_text' => '',
                        'type'      => 'list',
                        'end_size'  => 3,
                        'mid_size'  => 3,
                    ));
                    ?>
                </div>
                <div class="pagination__arrows">
                    <?php if ($current_page > 1) : ?>
                        <a href="<?php echo esc_url(get_pagenum_link($current_page - 1)); ?>">Previous</a>
                    <?php endif; ?>
                    <?php if ($current_page < $query->max_num_pages) : ?>
                        <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="woocommerce-info">' . __('No products found', 'woocommerce') . '</p>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $html,
    ));
}

/**
 * Добавляем JavaScript для AJAX фильтрации
 */
add_action('wp_footer', 'customshop_ajax_filter_js');

function customshop_ajax_filter_js() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(function($) {
        // AJAX сортировка
        $(document).on('change', '.woocommerce-ordering select.orderby', function(e) {
            e.preventDefault();

            var orderby = $(this).val();
            var $container = $('.catalog .container');

            // Показываем loader
            $container.css('opacity', '0.5');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'filter_products',
                    orderby: orderby,
                    paged: 1
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data.html);
                        $container.css('opacity', '1');

                        // Обновляем URL
                        var url = new URL(window.location);
                        url.searchParams.set('orderby', orderby);
                        url.searchParams.delete('paged');
                        window.history.pushState({}, '', url);

                        // Скролл наверх
                        $('html, body').animate({
                            scrollTop: $('.catalog__head').offset().top - 100
                        }, 500);
                    }
                },
                error: function() {
                    $container.css('opacity', '1');
                }
            });
        });

        // AJAX пагинация
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();

            var href = $(this).attr('href');
            var page = 1;

            // Пытаемся извлечь номер страницы из URL
            var match = href.match(/paged=(\d+)/);
            if (match) {
                page = match[1];
            } else {
                match = href.match(/page\/(\d+)/);
                if (match) {
                    page = match[1];
                }
            }

            var orderby = $('.woocommerce-ordering select.orderby').val();
            var $container = $('.catalog .container');

            // Показываем loader
            $container.css('opacity', '0.5');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'filter_products',
                    orderby: orderby,
                    paged: page
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data.html);
                        $container.css('opacity', '1');

                        // Обновляем URL
                        var url = new URL(window.location);
                        if (page > 1) {
                            url.searchParams.set('paged', page);
                        } else {
                            url.searchParams.delete('paged');
                        }
                        window.history.pushState({}, '', url);

                        // Скролл наверх
                        $('html, body').animate({
                            scrollTop: $('.catalog__head').offset().top - 100
                        }, 500);
                    }
                },
                error: function() {
                    $container.css('opacity', '1');
                }
            });
        });
    });
    </script>
    <?php
}
