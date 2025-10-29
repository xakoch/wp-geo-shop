<?php
/**
 * Custom Shop Theme Functions
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Theme Setup and Configuration
 */
require_once get_template_directory() . '/inc/theme-setup.php';

/**
 * WooCommerce Support
 */
require_once get_template_directory() . '/inc/woocommerce-setup.php';

/**
 * Enqueue Styles and Scripts
 */
require_once get_template_directory() . '/inc/enqueue-assets.php';

/**
 * Widget Areas
 */
require_once get_template_directory() . '/inc/widgets.php';

/**
 * AJAX Cart Functionality
 */
require_once get_template_directory() . '/inc/ajax-cart.php';

/**
 * AJAX Authentication
 */
require_once get_template_directory() . '/inc/ajax-auth.php';

/**
 * AJAX Products (Variations and Additional Products)
 */
require_once get_template_directory() . '/inc/ajax-products.php';

/**
 * AJAX Shop Filtering
 */
require_once get_template_directory() . '/inc/ajax-filters.php';

/**
 * WooCommerce Customization
 */
require_once get_template_directory() . '/inc/woocommerce-customization.php';



// Подключаем AJAX скрипт для корзины
add_action('wp_enqueue_scripts', 'customshop_cart_ajax_script', 20);
function customshop_cart_ajax_script() {
    if (is_cart()) {
        $cart_ajax_version = filemtime(get_template_directory() . '/assets/js/cart-ajax.js');
        
        wp_enqueue_script(
            'customshop-cart-ajax', 
            get_template_directory_uri() . '/assets/js/cart-ajax.js', 
            array('jquery', 'wc-cart'), 
            $cart_ajax_version, 
            true
        );
        
        wp_localize_script('customshop-cart-ajax', 'wc_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'update_cart_nonce' => wp_create_nonce('update-cart')
        ));
    }
}

// AJAX удаление товара из корзины
add_action('wp_ajax_remove_cart_item', 'customshop_ajax_remove_cart_item_v2');
add_action('wp_ajax_nopriv_remove_cart_item', 'customshop_ajax_remove_cart_item_v2');

function customshop_ajax_remove_cart_item_v2() {
    if (!isset($_POST['cart_item_key'])) {
        wp_send_json_error(array('message' => 'Missing cart_item_key'));
        wp_die();
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $removed = WC()->cart->remove_cart_item($cart_item_key);

    if ($removed) {
        WC()->cart->calculate_totals();
        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error(array('message' => 'Failed to remove item'));
    }

    wp_die();
}