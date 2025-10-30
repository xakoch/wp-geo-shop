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

/**
 * Customize checkout fields layout
 */
add_filter('woocommerce_checkout_fields', 'customshop_checkout_fields');
function customshop_checkout_fields($fields) {
    // Customize billing fields
    foreach ($fields['billing'] as $key => $field) {
        // Remove labels for cleaner design
        $fields['billing'][$key]['label'] = '';

        // Set custom placeholders and classes
        switch ($key) {
            case 'billing_email':
                $fields['billing'][$key]['placeholder'] = __('Email address', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-wide');
                break;
            case 'billing_country':
                $fields['billing'][$key]['placeholder'] = __('Country/Region', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-wide');
                break;
            case 'billing_first_name':
                $fields['billing'][$key]['placeholder'] = __('First Name', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-first');
                break;
            case 'billing_last_name':
                $fields['billing'][$key]['placeholder'] = __('Last Name', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-last');
                break;
            case 'billing_address_1':
                $fields['billing'][$key]['placeholder'] = __('Address', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-wide');
                break;
            case 'billing_city':
                $fields['billing'][$key]['placeholder'] = __('City', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-first');
                break;
            case 'billing_state':
                $fields['billing'][$key]['placeholder'] = __('State/County', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-last');
                break;
            case 'billing_postcode':
                $fields['billing'][$key]['placeholder'] = __('Postal code', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-first');
                break;
            case 'billing_phone':
                $fields['billing'][$key]['placeholder'] = __('Phone (optional)', 'woocommerce');
                $fields['billing'][$key]['class'] = array('form-row-last');
                $fields['billing'][$key]['required'] = false;
                break;
        }
    }

    return $fields;
}

/**
 * Customize checkout field output to match HTML structure
 */
add_filter('woocommerce_form_field', 'customshop_checkout_field_wrapper', 10, 4);
function customshop_checkout_field_wrapper($field, $key, $args, $value) {
    // For checkout page only
    if (!is_checkout()) {
        return $field;
    }

    // Add custom class wrapper
    $field = str_replace('form-row', 'form-row form-row-grid', $field);

    return $field;
}
