<?php
/**
 * Empty cart page
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

do_action('woocommerce_cart_is_empty');
?>

<section class="catalog__head">
    <div class="catalog__head-title">
        <h1 class="page-title"><?php _e('Cart', 'woocommerce'); ?></h1>
    </div>
</section>

<section class="cart-page">
    <div class="container">
        <div class="cart-empty-message">
            <p class="cart-empty woocommerce-info">
                <?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?>
            </p>
        </div>

        <?php do_action('woocommerce_cart_is_empty_actions'); ?>

        <p class="return-to-shop">
            <a class="button wc-backward btn btn--primary" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
                <?php esc_html_e('Return to shop', 'woocommerce'); ?>
            </a>
        </p>
    </div>
</section>

<?php // Failed to remove item from cart ?>