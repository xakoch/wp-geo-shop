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

    // jQuery (уже включен в WordPress)
    wp_enqueue_script('jquery');

    // Подключаем скрипты
    wp_enqueue_script('customshop-lib', get_template_directory_uri() . '/assets/js/lib.min.js', array('jquery'), $lib_version, true);
    wp_enqueue_script('customshop-app', get_template_directory_uri() . '/assets/js/app.min.js', array('jquery', 'customshop-lib'), $app_version, true);
    wp_enqueue_script('customshop-add', get_template_directory_uri() . '/assets/js/add.min.js', array('jquery', 'customshop-app'), $add_version, true);

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

    // Локализация скриптов для AJAX
    wp_localize_script('customshop-add', 'wc_add_to_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
        'checkout_url' => wc_get_checkout_url(),
    ));
}
add_action('wp_enqueue_scripts', 'customshop_scripts');
