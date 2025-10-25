<?php
/**
 * WooCommerce Support and Configuration
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Add WooCommerce support to theme
 */
function customshop_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'customshop_woocommerce_setup');

/**
 * Disable default WooCommerce styles
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Ensure WooCommerce session is started for AJAX requests
 */
function customshop_init_woocommerce_session() {
    if (class_exists('WooCommerce')) {
        // Make sure cart is loaded
        if (is_null(WC()->cart)) {
            WC()->frontend_includes();
            WC()->cart = new WC_Cart();
            WC()->customer = new WC_Customer();
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
        }

        // Ensure session is started
        if (WC()->session && !WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }
}
add_action('init', 'customshop_init_woocommerce_session', 5);
add_action('wp_loaded', 'customshop_init_woocommerce_session', 5);
