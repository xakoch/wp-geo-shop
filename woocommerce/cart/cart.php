<?php
/**
 * Cart Page
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

do_action('woocommerce_before_cart');
?>

<section class="catalog__head">
    <div class="catalog__head-title">
        <h1 class="page-title">Cart</h1>
    </div>
</section>

<!-- WooCommerce Notices -->
<?php if (wc_notice_count() > 0) : ?>
<div class="container">
    <?php woocommerce_output_all_notices(); ?>
</div>
<?php endif; ?>

<section class="cart-page">
    <div class="container">
        <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <?php do_action('woocommerce_before_cart_table'); ?>
            
            <div class="cart-page__wrapper">
                <!-- Левая колонка с товарами -->
                <div class="cart-page__products">
                    <div class="cart-page__header">
                        <div class="cart-page__header-product">Product</div>
                        <div class="cart-page__header-total">Total</div>
                    </div>

                    <?php do_action('woocommerce_before_cart_contents'); ?>

                    <?php
                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                            $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('full'), $cart_item, $cart_item_key);
                            ?>
                            <!-- Товар -->
                            <div class="cart-item woocommerce-cart-form__cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                <!-- Колонка 1: Фото -->
                                <div class="cart-item__image">
                                    <?php
                                    if (!$product_permalink) {
                                        echo $thumbnail;
                                    } else {
                                        printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                                    }
                                    ?>
                                </div>

                                <!-- Колонка 2: Инфо -->
                                <div class="cart-item__info">
                                    <div class="cart-item__top">
                                        <h3 class="cart-item__title">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post($_product->get_name());
                                            } else {
                                                printf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($_product->get_name()));
                                            }
                                            ?>
                                        </h3>
                                        <div class="cart-item__price"><?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?></div>
                                    </div>
                                    
                                    <?php if (!empty($cart_item['variation'])) :
                                        // Подсчитываем количество атрибутов
                                        $attributes_count = count(array_filter($cart_item['variation'], function($value) {
                                            return !empty($value);
                                        }));
                                    ?>
                                    <div class="cart-item__meta">
                                        <?php
                                        $attr_index = 0;
                                        foreach ($cart_item['variation'] as $attr_key => $attr_value) :
                                            if (empty($attr_value)) continue;

                                            $taxonomy = str_replace('attribute_', '', $attr_key);
                                            $term = get_term_by('slug', $attr_value, $taxonomy);
                                            $label = wc_attribute_label($taxonomy);
                                            $is_color = (strpos(strtolower($taxonomy), 'color') !== false);

                                            // Добавляем border-bottom для первого атрибута, если всего атрибутов 2
                                            $style = '';
                                            if ($attributes_count == 2 && $attr_index == 0) {
                                                $style = ' style="border-bottom: 1px solid rgba(0,0,0,0.1);"';
                                            }
                                            $attr_index++;
                                        ?>
                                            <div class="cart-item__attribute"<?php echo $style; ?>>
                                                <span class="cart-item__attribute-label"><?php echo esc_html($label); ?></span>
                                                <span class="cart-item__attribute-value">
                                                    <?php if ($is_color && $term) :
                                                        $color = get_term_meta($term->term_id, 'color', true);
                                                        if (empty($color)) {
                                                            $color = customshop_get_color_from_name($term->name);
                                                        }
                                                    ?>
                                                        <span class="color-dot" style="background: <?php echo esc_attr($color); ?>;"></span>
                                                    <?php else : ?>
                                                        <?php echo $term ? esc_html($term->name) : esc_html($attr_value); ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Колонка 3: Действия -->
                                <div class="cart-item__actions">
                                    <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
                                       class="cart-item__remove remove_from_cart_button ajax_remove_from_cart"
                                       aria-label="Remove this item"
                                       data-product_id="<?php echo esc_attr($product_id); ?>"
                                       data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14 4H13V14C13 14.2652 12.8946 14.5195 12.707 14.707C12.5195 14.8946 12.2652 15 12 15H4C3.73478 15 3.48051 14.8946 3.29297 14.707C3.10543 14.5195 3 14.2652 3 14V4H2V3H14V4ZM4 14H12V4H4V14ZM7 12H6V6H7V12ZM10 12H9V6H10V12ZM10 1V2H6V1H10Z" fill="#F52222"/>
                                        </svg>
                                        Remove
                                    </a>
                                    <div class="cart-item__quantity"
                                         data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
                                         data-min="1"
                                         data-max="<?php echo $_product->get_max_purchase_quantity(); ?>">
                                        <button type="button" class="qty-btn qty-btn--minus">−</button>
                                        <span class="qty-display"><?php echo $cart_item['quantity']; ?></span>
                                        <input type="hidden"
                                               name="cart[<?php echo $cart_item_key; ?>][qty]"
                                               value="<?php echo $cart_item['quantity']; ?>"
                                               class="qty-value" />
                                        <button type="button" class="qty-btn qty-btn--plus">+</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php do_action('woocommerce_cart_contents'); ?>
                    <?php do_action('woocommerce_after_cart_contents'); ?>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </div>

                <!-- Правая колонка с итогами -->
                <div class="cart-page__sidebar">
                    <div class="cart-totals">
                        <h2 class="cart-totals__title">Cart totals</h2>
                        
                        <div class="cart-totals__coupon">
                            <button type="button" class="cart-totals__coupon-toggle" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'">
                                <span>Add coupons</span>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <div style="display: none; margin-top: 12px;">
                                <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
                                <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
                            </div>
                        </div>

                        <div class="cart-totals__row cart-totals__total">
                            <span>Estimated total</span>
                            <span class="cart-totals__price"><?php wc_cart_totals_order_total_html(); ?></span>
                        </div>
                    </div>
                    
                    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="btn btn--primary cart-totals__checkout">Proceed to Checkout</a>
                </div>
            </div>

            <?php do_action('woocommerce_after_cart_table'); ?>
        </form>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div id="cart-delete-modal" class="cart-modal" style="display: none;">
    <div class="cart-modal__overlay"></div>
    <div class="cart-modal__content">
        <div class="cart-modal__header">
            <h3 class="cart-modal__title">Remove Item</h3>
            <button type="button" class="cart-modal__close" aria-label="Close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <div class="cart-modal__body">
            <p>Are you sure you want to remove this item from your cart?</p>
        </div>
        <div class="cart-modal__footer">
            <button type="button" class="btn btn--secondary cart-modal__cancel">Cancel</button>
            <button type="button" class="btn btn--danger cart-modal__confirm">Remove</button>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>