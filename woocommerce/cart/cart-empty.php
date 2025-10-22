<?php
/**
 * Empty cart page
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

do_action('woocommerce_cart_is_empty');
?>

<div class="cart-empty-message">
    <p class="cart-empty woocommerce-info">
        <?php esc_html_e('Ваша корзина пуста.', 'woocommerce'); ?>
    </p>
</div>

<?php do_action('woocommerce_cart_is_empty_actions'); ?>

<p class="return-to-shop">
    <a class="button wc-backward<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
        <?php esc_html_e('Вернуться в магазин', 'woocommerce'); ?>
    </a>
</p>