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
