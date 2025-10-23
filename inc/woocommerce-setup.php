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
