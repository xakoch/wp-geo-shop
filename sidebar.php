<?php
/**
 * Sidebar Template
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

// Не показывать сайдбар на страницах WooCommerce с полной шириной
if (is_woocommerce() && (is_cart() || is_checkout() || is_account_page())) {
    return;
}

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area">
    <?php dynamic_sidebar('sidebar-1'); ?>
</aside><!-- #secondary -->