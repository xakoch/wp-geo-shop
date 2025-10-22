<?php
/**
 * My Account Page
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Hook: woocommerce_account_navigation
 * Навигация личного кабинета
 */
do_action('woocommerce_account_navigation');
?>

<div class="woocommerce-MyAccount-content">
    <?php
    /**
     * Hook: woocommerce_account_content
     * Контент страниц личного кабинета
     */
    do_action('woocommerce_account_content');
    ?>
</div>