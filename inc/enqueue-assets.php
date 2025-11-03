<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue theme styles and scripts
 */
function customshop_scripts() {
    $theme_version = wp_get_theme()->get('Version');

    // Get file modification times for cache busting
    $style_version = filemtime(get_template_directory() . '/assets/css/style.min.css');
    $modals_version = filemtime(get_template_directory() . '/assets/css/modals.css');
    $lib_version = filemtime(get_template_directory() . '/assets/js/lib.min.js');
    $app_version = filemtime(get_template_directory() . '/assets/js/app.min.js');
    $add_version = filemtime(get_template_directory() . '/assets/js/add.min.js');
    $quantity_version = filemtime(get_template_directory() . '/assets/js/quantity.js');

    // Подключаем основные стили
    wp_enqueue_style('customshop-main', get_template_directory_uri() . '/assets/css/style.min.css', array(), $style_version);

    // Подключаем стили модальных окон
    wp_enqueue_style('customshop-modals', get_template_directory_uri() . '/assets/css/modals.css', array('customshop-main'), $modals_version);

    // Подключаем стили уведомлений
    $notifications_version = filemtime(get_template_directory() . '/assets/css/notifications.css');
    wp_enqueue_style('customshop-notifications', get_template_directory_uri() . '/assets/css/notifications.css', array('customshop-main'), $notifications_version);

    // Подключаем стили мини-корзины
    $minicart_version = filemtime(get_template_directory() . '/assets/css/mini-cart.css');
    wp_enqueue_style('customshop-mini-cart', get_template_directory_uri() . '/assets/css/mini-cart.css', array('customshop-main'), $minicart_version);

    // Подключаем стили header dropdown
    $header_dropdown_version = filemtime(get_template_directory() . '/assets/css/header-dropdown.css');
    wp_enqueue_style('customshop-header-dropdown', get_template_directory_uri() . '/assets/css/header-dropdown.css', array('customshop-main'), $header_dropdown_version);

    // Подключаем стили Contact Form 7
    $contact_form_version = filemtime(get_template_directory() . '/assets/css/contact-form.css');
    wp_enqueue_style('customshop-contact-form', get_template_directory_uri() . '/assets/css/contact-form.css', array('customshop-main'), $contact_form_version);

    // Подключаем стили авторизации
    $auth_css_version = filemtime(get_template_directory() . '/assets/css/auth.css');
    wp_enqueue_style('customshop-auth', get_template_directory_uri() . '/assets/css/auth.css', array('customshop-main'), $auth_css_version);

    // Подключаем стили single product страницы
    if (is_product()) {
        $single_product_version = filemtime(get_template_directory() . '/assets/css/single-product.css');
        wp_enqueue_style('customshop-single-product', get_template_directory_uri() . '/assets/css/single-product.css', array('customshop-main'), $single_product_version);
    }

    // Подключаем стили checkout и cart страниц
    if (is_checkout() || is_cart()) {
        $checkout_version = filemtime(get_template_directory() . '/assets/css/checkout.css');
        wp_enqueue_style('customshop-checkout', get_template_directory_uri() . '/assets/css/checkout.css', array('customshop-main'), $checkout_version);
    }

    // Подключаем скрипт для checkout страницы
    if (is_checkout()) {
        $checkout_js_version = filemtime(get_template_directory() . '/assets/js/checkout.js');
        wp_enqueue_script('customshop-checkout-js', get_template_directory_uri() . '/assets/js/checkout.js', array('jquery'), $checkout_js_version, true);

        // Добавляем inline стили для форм checkout (загружаются последними)
        $checkout_inline_css = '
        .checkout-page .checkout-section .input-text,
        .checkout-page .checkout-section input[type="text"],
        .checkout-page .checkout-section input[type="email"],
        .checkout-page .checkout-section input[type="tel"] {
            width: 100% !important;
            padding: 12px 16px !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            font-size: 16px !important;
            font-family: Manrope, sans-serif !important;
            font-weight: 400 !important;
            color: #000 !important;
            background: #fff !important;
            transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
            box-sizing: border-box !important;
        }
        .checkout-page .checkout-section select,
        .checkout-page .checkout-section .country_select,
        .checkout-page .checkout-section .state_select {
            width: 100% !important;
            padding: 12px 16px !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            font-size: 16px !important;
            font-family: Manrope, sans-serif !important;
            font-weight: 400 !important;
            color: #000 !important;
            background-color: #fff !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg width=\'12\' height=\'8\' viewBox=\'0 0 12 8\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M1 1L6 6L11 1\' stroke=\'%23666\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            padding-right: 40px !important;
            cursor: pointer !important;
            transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
            box-sizing: border-box !important;
        }
        .checkout-page .checkout-section .input-text:focus,
        .checkout-page .checkout-section input:focus,
        .checkout-page .checkout-section select:focus {
            border-color: #176DAA !important;
            box-shadow: 0 0 0 3px rgba(23, 109, 170, 0.1) !important;
            outline: none !important;
        }
        .checkout-page .checkout-section select:hover {
            border-color: #999 !important;
        }
        ';
        wp_add_inline_style('customshop-checkout', $checkout_inline_css);
    }

    // jQuery (уже включен в WordPress)
    wp_enqueue_script('jquery');

    // Подключаем скрипты
    wp_enqueue_script('customshop-lib', get_template_directory_uri() . '/assets/js/lib.min.js', array('jquery'), $lib_version, true);
    wp_enqueue_script('customshop-app', get_template_directory_uri() . '/assets/js/app.min.js', array('jquery', 'customshop-lib'), $app_version, true);
    wp_enqueue_script('customshop-add', get_template_directory_uri() . '/assets/js/add.min.js', array('jquery', 'customshop-app'), $add_version, true);

    // Header dropdown
    $header_dropdown_js_version = filemtime(get_template_directory() . '/assets/js/header-dropdown.js');
    wp_enqueue_script('customshop-header-dropdown', get_template_directory_uri() . '/assets/js/header-dropdown.js', array('jquery'), $header_dropdown_js_version, true);

    // Подключаем скрипт для single product page
    if (is_product()) {
        $single_product_version = filemtime(get_template_directory() . '/assets/js/single-product.js');
        // Force cache bust with timestamp
        wp_enqueue_script('customshop-single-product', get_template_directory_uri() . '/assets/js/single-product.js', array('jquery', 'customshop-add'), $single_product_version . '-' . time(), true);
    } else {
        // Quantity только для других страниц (cart, checkout)
        wp_enqueue_script('customshop-quantity', get_template_directory_uri() . '/assets/js/quantity.js', array('jquery'), $quantity_version, true);
    }

    // Подключаем скрипт для карточек товаров на страницах магазина и главной
    if (is_shop() || is_product_category() || is_product_tag() || is_front_page()) {
        $product_cards_version = filemtime(get_template_directory() . '/assets/js/product-cards.js');
        wp_enqueue_script('customshop-product-cards', get_template_directory_uri() . '/assets/js/product-cards.js', array('jquery', 'customshop-add'), $product_cards_version, true);
    }

    // Подключаем AJAX фильтры для страниц магазина
    if (is_shop() || is_product_category() || is_product_tag()) {
        $filters_version = filemtime(get_template_directory() . '/assets/js/ajax-filters.js');
        wp_enqueue_script('customshop-ajax-filters', get_template_directory_uri() . '/assets/js/ajax-filters.js', array('jquery'), $filters_version, true);

        // Локализация для AJAX фильтров
        wp_localize_script('customshop-ajax-filters', 'customshop_ajax_filters', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }

    // Подключаем скрипт авторизации
    $auth_version = filemtime(get_template_directory() . '/assets/js/auth.js');
    wp_enqueue_script('customshop-auth', get_template_directory_uri() . '/assets/js/auth.js', array('jquery', 'customshop-add'), $auth_version, true);

    // Локализация для auth.js
    wp_localize_script('customshop-auth', 'authParams', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));

    // Локализация скриптов для AJAX
    wp_localize_script('customshop-add', 'wc_add_to_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
        'checkout_url' => wc_get_checkout_url(),
    ));
}
add_action('wp_enqueue_scripts', 'customshop_scripts');
