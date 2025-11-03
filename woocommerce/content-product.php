<?php
/**
 * The template for displaying product content within loops
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) exit;

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}

// Get product variations for color count
$available_variations = $product->is_type('variable') ? $product->get_available_variations() : array();
$colors_count = count($available_variations);

// Check if product is on sale
$is_on_sale = $product->is_on_sale();
$sale_percentage = 0;
if ($is_on_sale) {
    $regular_price = (float) $product->get_regular_price();
    $sale_price = (float) $product->get_sale_price();
    if ($regular_price > 0) {
        $sale_percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
    }
}
?>

<div class="product">
    <?php if ($colors_count > 0) : ?>
        <span class="colors-count"><?php echo esc_html($colors_count); ?> <?php _e('colors', 'kerning-geoshop'); ?></span>
    <?php endif; ?>

    <?php if ($is_on_sale && $sale_percentage > 0) : ?>
        <span class="sale-percent">-<?php echo esc_html($sale_percentage); ?>%</span>
    <?php endif; ?>

    <div class="product__img">
        <a href="<?php echo esc_url(get_permalink()); ?>">
            <?php echo woocommerce_get_product_thumbnail('woocommerce_full_size'); ?>
        </a>
    </div>

    <div class="product__footer">
        <div class="product__footer-info">
            <div class="product__title">
                <a href="<?php echo esc_url(get_permalink()); ?>">
                    <h3><?php echo esc_html(get_the_title()); ?></h3>
                </a>
            </div>
            <div class="product__price">
                <?php if ($is_on_sale) : ?>
                    <span class="price"><?php echo wc_price($product->get_sale_price()); ?></span>
                    <span class="last-price"><?php echo wc_price($product->get_regular_price()); ?></span>
                <?php else : ?>
                    <span class="price"><?php echo wc_price($product->get_price()); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="product__footer-action">
            <?php if ($product->is_type('variable')) : ?>
                <button class="add-to-cart open-variation-modal" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="<?php _e('Select options', 'kerning-geoshop'); ?>">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8574 0V9.14258H20V10.8574H10.8574V20H9.14258V10.8574H0V9.14258H9.14258V0H10.8574Z" fill="#176DAA"/>
                    </svg>
                </button>
            <?php else : ?>
                <button class="add-to-cart" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="<?php _e('Add to cart', 'kerning-geoshop'); ?>">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8574 0V9.14258H20V10.8574H10.8574V20H9.14258V10.8574H0V9.14258H9.14258V0H10.8574Z" fill="#176DAA"/>
                    </svg>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>