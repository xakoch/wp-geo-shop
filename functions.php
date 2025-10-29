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



// Подключаем AJAX скрипт и стили для корзины
add_action('wp_enqueue_scripts', 'customshop_cart_ajax_script', 20);
function customshop_cart_ajax_script() {
    if (is_cart()) {
        $cart_ajax_version = filemtime(get_template_directory() . '/assets/js/cart-ajax.js');
        $cart_modal_css_version = filemtime(get_template_directory() . '/assets/css/cart-modal.css');

        // Подключаем стили для модального окна
        wp_enqueue_style(
            'customshop-cart-modal',
            get_template_directory_uri() . '/assets/css/cart-modal.css',
            array(),
            $cart_modal_css_version
        );

        // Подключаем скрипт корзины
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

// AJAX handlers are now in inc/ajax-cart.php