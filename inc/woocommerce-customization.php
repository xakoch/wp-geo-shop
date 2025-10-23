<?php
/**
 * WooCommerce Customization and Utilities
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

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

/**
 * Отключаем стандартные WooCommerce хуки (управляем всем в archive-product.php)
 */
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
