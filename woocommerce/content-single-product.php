<?php
/**
 * The template for displaying single product content
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

global $product;

/**
 * Hook: woocommerce_before_single_product
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>

    <?php
    /**
     * Hook: woocommerce_before_single_product_summary
     * Галерея изображений
     */
    do_action('woocommerce_before_single_product_summary');
    ?>

    <div class="summary entry-summary">
        <?php
        /**
         * Hook: woocommerce_single_product_summary
         * 
         * @hooked woocommerce_template_single_title - 5
         * @hooked woocommerce_template_single_rating - 10
         * @hooked woocommerce_template_single_price - 10
         * @hooked woocommerce_template_single_excerpt - 20
         * @hooked woocommerce_template_single_add_to_cart - 30
         * @hooked woocommerce_template_single_meta - 40
         * @hooked woocommerce_template_single_sharing - 50
         */
        do_action('woocommerce_single_product_summary');
        ?>
    </div>

    <?php
    /**
     * Hook: woocommerce_after_single_product_summary
     * Табы (описание, отзывы), похожие товары
     */
    do_action('woocommerce_after_single_product_summary');
    ?>
</div>

<?php do_action('woocommerce_after_single_product'); ?>