<?php
/**
 * WooCommerce Support and Configuration
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Remove automatic <p> and <br> tags from Contact Form 7
 */
add_filter('wpcf7_autop_or_not', '__return_false');

/**
 * Remove Downloads from WooCommerce account menu
 */
function customshop_remove_downloads_from_account_menu($items) {
    unset($items['downloads']);
    return $items;
}
add_filter('woocommerce_account_menu_items', 'customshop_remove_downloads_from_account_menu');

/**
 * Redirect to home page after logout
 */
function customshop_logout_redirect() {
    return home_url();
}
add_filter('woocommerce_logout_default_redirect_url', 'customshop_logout_redirect');

/**
 * Disable Select2 on checkout and my-account pages for country/state fields
 */
function customshop_disable_select2_on_checkout() {
    if (is_checkout() || is_account_page()) {
        wp_dequeue_script('select2');
        wp_deregister_script('select2');
        wp_dequeue_style('select2');
        wp_deregister_style('select2');

        // Also disable WooCommerce's selectWoo (Select2 wrapper)
        wp_dequeue_script('selectWoo');
        wp_deregister_script('selectWoo');
        wp_dequeue_style('select2');
        wp_deregister_style('select2');
    }
}
add_action('wp_enqueue_scripts', 'customshop_disable_select2_on_checkout', 100);

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
 * Force woocommerce.php template for all WooCommerce pages
 */
function customshop_woocommerce_template($template) {
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        $woocommerce_template = locate_template('woocommerce.php');
        if ($woocommerce_template) {
            return $woocommerce_template;
        }
    }
    return $template;
}
add_filter('template_include', 'customshop_woocommerce_template', 99);

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

/**
 * Remove default WooCommerce notices output from all hooks
 */
function customshop_remove_default_notices() {
    remove_action('woocommerce_before_main_content', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);

    // Remove coupon form from before checkout form (we'll add it manually)
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
}
add_action('init', 'customshop_remove_default_notices');
