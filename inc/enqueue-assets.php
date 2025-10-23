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
    // Подключаем основные стили
    wp_enqueue_style('customshop-main', get_template_directory_uri() . '/assets/css/style.min.css', array(), '1.0.1');

    // Подключаем стили модальных окон
    wp_enqueue_style('customshop-modals', get_template_directory_uri() . '/assets/css/modals.css', array('customshop-main'), '1.0.1');

    // jQuery (уже включен в WordPress)
    wp_enqueue_script('jquery');

    // Подключаем скрипты
    wp_enqueue_script('customshop-lib', get_template_directory_uri() . '/assets/js/lib.min.js', array('jquery'), '1.0.1', true);
    wp_enqueue_script('customshop-app', get_template_directory_uri() . '/assets/js/app.min.js', array('jquery', 'customshop-lib'), '1.0.1', true);
    wp_enqueue_script('customshop-add', get_template_directory_uri() . '/assets/js/add.min.js', array('jquery', 'customshop-app'), '1.0.1', true);
    wp_enqueue_script('customshop-quantity', get_template_directory_uri() . '/assets/js/quantity.js', array('jquery'), '1.0.1', true);

    // Подключаем AJAX фильтры для страниц магазина
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script('customshop-ajax-filters', get_template_directory_uri() . '/assets/js/ajax-filters.js', array('jquery'), '1.0.1', true);

        // Локализация для AJAX фильтров
        wp_localize_script('customshop-ajax-filters', 'customshop_ajax_filters', array(
            'ajax_url' => admin_url('admin-ajax.php'),
        ));
    }

    // Локализация скриптов для AJAX
    wp_localize_script('customshop-add', 'wc_add_to_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
    ));
}
add_action('wp_enqueue_scripts', 'customshop_scripts');
