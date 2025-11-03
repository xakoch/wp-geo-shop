<?php
/**
 * Checkout Form
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) exit;

// Если не залогинен и требуется регистрация
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<section class="catalog__head">
    <div class="catalog__head-title">
        <h1 class="page-title"><?php _e('Checkout', 'woocommerce'); ?></h1>
    </div>
</section>

<!-- WooCommerce Notices -->
<?php if (wc_notice_count() > 0) : ?>
<div class="container">
    <?php
    // Output notices
    woocommerce_output_all_notices();

    // Call the hook but notices and coupon already removed
    do_action('woocommerce_before_checkout_form', $checkout);
    ?>
</div>
<?php endif; ?>

<section class="checkout-page woocommerce-checkout">
    <div class="container">
        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

            <?php if ($checkout->get_checkout_fields()) : ?>

            <div class="checkout-page__wrapper">
                <!-- Левая колонка с формами -->
                <div class="checkout-page__main">
                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                    <div class="checkout-page__form">

                    <?php
                        $fields = $checkout->get_checkout_fields();
                    ?>

                    <!-- Contact information -->
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <h3 class="checkout-section__title"><?php _e('Contact information', 'woocommerce'); ?></h3>
                            <p class="checkout-section__subtitle"><?php _e("We'll use this email to send you details and updates about your order.", 'woocommerce'); ?></p>
                        </div>

                        <div class="form-row form-row-grid">
                            <input type="email"
                                   class="input-text"
                                   name="billing_email"
                                   id="billing_email"
                                   placeholder="<?php esc_attr_e('Email address', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>"
                                   required />
                        </div>

                        <?php if (!is_user_logged_in()) : ?>
                        <p class="checkout-notice"><?php _e('You are currently checking out as a guest.', 'woocommerce'); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Billing address -->
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <h3 class="checkout-section__title"><?php _e('Billing address', 'woocommerce'); ?></h3>
                            <p class="checkout-section__subtitle"><?php _e('Enter the billing address that matches your payment method.', 'woocommerce'); ?></p>
                        </div>

                        <!-- Country -->
                        <div class="form-row form-row-grid">
                            <select class="country_select" name="billing_country" id="billing_country" required>
                                <option value=""><?php _e('Country/Region', 'woocommerce'); ?></option>
                                <?php
                                // Try to get countries from WooCommerce
                                $countries = array();

                                if (function_exists('WC') && WC()->countries) {
                                    $countries = WC()->countries->get_countries();
                                }

                                // Fallback to hardcoded countries if empty
                                if (empty($countries)) {
                                    $countries = array(
                                        'LV' => 'Latvia',
                                        'EE' => 'Estonia',
                                        'LT' => 'Lithuania',
                                        'US' => 'United States',
                                        'GB' => 'United Kingdom',
                                        'DE' => 'Germany',
                                        'FR' => 'France',
                                        'ES' => 'Spain',
                                        'IT' => 'Italy',
                                        'PL' => 'Poland',
                                        'SE' => 'Sweden',
                                        'NO' => 'Norway',
                                        'FI' => 'Finland',
                                        'DK' => 'Denmark',
                                        'NL' => 'Netherlands',
                                        'BE' => 'Belgium',
                                        'AT' => 'Austria',
                                        'CH' => 'Switzerland',
                                        'CZ' => 'Czech Republic',
                                        'RU' => 'Russia'
                                    );
                                }

                                foreach ($countries as $country_code => $country_name) {
                                    $selected = ($checkout->get_value('billing_country') == $country_code) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($country_code) . '" ' . $selected . '>' . esc_html($country_name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- First Name & Last Name -->
                        <div class="form-row form-row-grid">
                            <input type="text"
                                   class="input-text"
                                   name="billing_first_name"
                                   id="billing_first_name"
                                   placeholder="<?php esc_attr_e('First Name', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>"
                                   required />
                            <input type="text"
                                   class="input-text"
                                   name="billing_last_name"
                                   id="billing_last_name"
                                   placeholder="<?php esc_attr_e('Last Name', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_last_name')); ?>"
                                   required />
                        </div>

                        <!-- Address -->
                        <div class="form-row form-row-grid">
                            <input type="text"
                                   class="input-text"
                                   name="billing_address_1"
                                   id="billing_address_1"
                                   placeholder="<?php esc_attr_e('Address', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_address_1')); ?>"
                                   required />
                        </div>

                        <!-- City & State -->
                        <div class="form-row form-row-grid">
                            <input type="text"
                                   class="input-text"
                                   name="billing_city"
                                   id="billing_city"
                                   placeholder="<?php esc_attr_e('City', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_city')); ?>"
                                   required />
                            <input type="text"
                                   class="input-text"
                                   name="billing_state"
                                   id="billing_state"
                                   placeholder="<?php esc_attr_e('State/County', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_state')); ?>" />
                        </div>

                        <!-- Postcode & Phone -->
                        <div class="form-row form-row-grid">
                            <input type="text"
                                   class="input-text"
                                   name="billing_postcode"
                                   id="billing_postcode"
                                   placeholder="<?php esc_attr_e('Postal code', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_postcode')); ?>"
                                   required />
                            <input type="tel"
                                   class="input-text"
                                   name="billing_phone"
                                   id="billing_phone"
                                   placeholder="<?php esc_attr_e('Phone (optional)', 'woocommerce'); ?>"
                                   value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>" />
                        </div>
                    </div>

                    <!-- Shipping address (if needed) -->
                    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <h3 class="checkout-section__title"><?php _e('Shipping address', 'woocommerce'); ?></h3>
                        </div>

                        <?php do_action('woocommerce_checkout_shipping'); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Payment options -->
                    <div class="checkout-section">
                        <div class="checkout-section__header">
                            <h3 class="checkout-section__title"><?php _e('Payment options', 'woocommerce'); ?></h3>
                        </div>

                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div class="payment-methods">
                            <?php
                            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
                            if (!empty($available_gateways)) {
                                $gateway_index = 0;
                                foreach ($available_gateways as $gateway) {
                                    $gateway_index++;
                                    $is_first = ($gateway_index === 1);
                                    ?>
                                    <div class="payment-method <?php echo $is_first ? 'active' : ''; ?>">
                                        <input type="radio"
                                               id="payment_method_<?php echo esc_attr($gateway->id); ?>"
                                               name="payment_method"
                                               value="<?php echo esc_attr($gateway->id); ?>"
                                               <?php checked($is_first); ?> />
                                        <label for="payment_method_<?php echo esc_attr($gateway->id); ?>">
                                            <div class="payment-method__info">
                                                <div class="payment-method__header">
                                                    <span class="payment-method__title"><?php echo wp_kses_post($gateway->get_title()); ?></span>
                                                    <span class="payment-method__icon">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="10" cy="10" r="9" stroke="<?php echo $is_first ? '#176DAA' : '#000'; ?>" stroke-width="2"/>
                                                            <?php if ($is_first) : ?>
                                                            <circle cx="10" cy="10" r="5" fill="#176DAA"/>
                                                            <?php endif; ?>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <?php if ($gateway->get_description()) : ?>
                                                <p class="payment-method__desc"><?php echo wp_kses_post($gateway->get_description()); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($gateway->get_icon()) : ?>
                                            <div class="payment-method__logo">
                                                <?php echo wp_kses_post($gateway->get_icon()); ?>
                                            </div>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>

                    <!-- Terms -->
                    <?php if (wc_get_page_id('terms') > 0 && apply_filters('woocommerce_checkout_show_terms', true)) : ?>
                    <div class="checkout-terms">
                        <p>
                            <?php printf(
                                __('By proceeding with your purchase you agree to our %1$sTerms and Conditions%2$s and %3$sPrivacy Policy%4$s', 'woocommerce'),
                                '<a href="' . esc_url(wc_get_page_permalink('terms')) . '" target="_blank">',
                                '</a>',
                                '<a href="' . esc_url(wc_privacy_policy_page_id() ? get_permalink(wc_privacy_policy_page_id()) : '#') . '" target="_blank">',
                                '</a>'
                            ); ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Buttons -->
                    <div class="checkout-actions">
                        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="btn btn--secondary"><?php _e('Return to cart', 'woocommerce'); ?></a>
                        <button type="submit" class="btn btn--primary" name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e('Place order', 'woocommerce'); ?>">
                            <?php esc_html_e('Place order', 'woocommerce'); ?>
                        </button>
                    </div>

                    </div><!-- .checkout-page__form -->
                </div><!-- .checkout-page__main -->

                <!-- Правая колонка - Order summary -->
                <div class="checkout-page__sidebar">
                    <div class="order-summary">
                        <h3 class="order-summary__title"><?php _e('Order summary', 'woocommerce'); ?></h3>

                        <div class="order-summary__items">
                            <?php
                            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                                if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
                                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                    ?>
                                    <div class="order-summary__item">
                                        <div class="order-summary__item-image">
                                            <?php
                                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                            if (!$product_permalink) {
                                                echo $thumbnail;
                                            } else {
                                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                                            }
                                            ?>
                                        </div>
                                        <div class="order-summary__item-details">
                                            <div class="order-summary__item-details__top">
                                                <h4 class="order-summary__item-title">
                                                    <?php
                                                    if (!$product_permalink) {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                                    } else {
                                                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                                    }
                                                    ?>
                                                </h4>
                                                <p class="order-summary__item-price">
                                                    <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                                                </p>
                                            </div>
                                            <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="order-summary__item-remove" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14 4H13V14C13 14.2652 12.8946 14.5195 12.707 14.707C12.5195 14.8946 12.2652 15 12 15H4C3.73478 15 3.48051 14.8946 3.29297 14.707C3.10543 14.5195 3 14.2652 3 14V4H2V3H14V4ZM4 14H12V4H4V14ZM7 12H6V6H7V12ZM10 12H9V6H10V12ZM10 1V2H6V1H10Z" fill="#F52222"/>
                                                </svg>
                                                <?php _e('Remove', 'woocommerce'); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <!-- Coupon -->
                        <?php if (wc_coupons_enabled()) : ?>
                        <div class="order-summary__coupon">
                            <button type="button" class="order-summary__coupon-toggle" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'">
                                <span><?php _e('Add coupons', 'woocommerce'); ?></span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <div style="display: none; margin-top: 12px;">
                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                <button type="button" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Totals -->
                        <div class="order-summary__totals">
                            <div class="order-summary__row">
                                <span><?php _e('Subtotal', 'woocommerce'); ?></span>
                                <span><?php wc_cart_totals_subtotal_html(); ?></span>
                            </div>

                            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                            <div class="order-summary__row">
                                <span><?php wc_cart_totals_coupon_label($coupon); ?></span>
                                <span><?php wc_cart_totals_coupon_html($coupon); ?></span>
                            </div>
                            <?php endforeach; ?>

                            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                            <div class="order-summary__row">
                                <span><?php _e('Shipping', 'woocommerce'); ?></span>
                                <span><?php wc_cart_totals_shipping_html(); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                            <div class="order-summary__row">
                                <span><?php echo esc_html($fee->name); ?></span>
                                <span><?php wc_cart_totals_fee_html($fee); ?></span>
                            </div>
                            <?php endforeach; ?>

                            <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                                <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                                    <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                                    <div class="order-summary__row">
                                        <span><?php echo esc_html($tax->label); ?></span>
                                        <span><?php echo wp_kses_post($tax->formatted_amount); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                <div class="order-summary__row">
                                    <span><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
                                    <span><?php wc_cart_totals_taxes_total_html(); ?></span>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="order-summary__row order-summary__total">
                                <span><?php _e('Total to pay:', 'woocommerce'); ?></span>
                                <span class="order-summary__total-price"><?php wc_cart_totals_order_total_html(); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php endif; ?>

            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>

        </form>
    </div>
</section>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
