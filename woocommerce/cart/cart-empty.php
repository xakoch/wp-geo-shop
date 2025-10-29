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

<section class="cart-page cart-page--empty">
    <div class="container">
        <div class="cart-empty">
            <div class="cart-empty__icon">
                <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="60" fill="#F5F5F5"/>
                    <path d="M45 35H75L80 50H40L45 35Z" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <path d="M40 50H80V80C80 82.2091 78.2091 84 76 84H44C41.7909 84 40 82.2091 40 80V50Z" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <path d="M50 60V74" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round"/>
                    <path d="M60 60V74" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round"/>
                    <path d="M70 60V74" stroke="#CCCCCC" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <h2 class="cart-empty__title">Your cart is empty</h2>
            <p class="cart-empty__description">
                Looks like you haven't added anything to your cart yet.<br>
                Start shopping to fill it up!
            </p>

            <?php do_action('woocommerce_cart_is_empty_actions'); ?>

            <div class="cart-empty__actions">
                <a class="btn btn--primary btn--large" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10L3 10M3 10L6 7M3 10L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 3H14C15.6569 3 17 4.34315 17 6V14C17 15.6569 15.6569 17 14 17H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <?php esc_html_e('Continue Shopping', 'woocommerce'); ?>
                </a>
            </div>
        </div>
    </div>
</section>