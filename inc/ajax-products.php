<?php
/**
 * AJAX Product Variations and Additional Products
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * AJAX handler to get product variations
 */
add_action('wp_ajax_get_product_variations', 'customshop_ajax_get_product_variations');
add_action('wp_ajax_nopriv_get_product_variations', 'customshop_ajax_get_product_variations');

function customshop_ajax_get_product_variations() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Missing product_id');
        wp_die();
    }

    $product_id = absint($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid product');
        wp_die();
    }

    // Get product image
    $image_id = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_single') : wc_placeholder_img_src();

    // Get product attributes
    $attributes = array();
    $available_variations = $product->get_available_variations();

    foreach ($product->get_variation_attributes() as $attribute_name => $options) {
        $attribute_label = wc_attribute_label($attribute_name);

        // Normalize attribute name for JavaScript
        $normalized_name = 'attribute_' . sanitize_title($attribute_name);

        $attributes[$normalized_name] = array(
            'label' => $attribute_label,
            'options' => $options
        );
    }

    // Format variations
    $variations = array();
    foreach ($available_variations as $variation) {
        $variation_obj = wc_get_product($variation['variation_id']);

        // Normalize attribute keys in variation
        $normalized_attributes = array();
        foreach ($variation['attributes'] as $key => $value) {
            // Key already comes with 'attribute_' prefix from WooCommerce
            $normalized_attributes[$key] = $value;
        }

        $variations[] = array(
            'variation_id' => $variation['variation_id'],
            'attributes' => $normalized_attributes,
            'price_html' => $variation_obj->get_price_html(),
            'is_in_stock' => $variation['is_in_stock'],
            'image' => isset($variation['image']['url']) ? $variation['image']['url'] : $image_url
        );
    }

    // Check if product has additional products (upsells, cross-sells, or related)
    $has_additional = false;
    $upsell_ids = $product->get_upsell_ids();
    $crosssell_ids = $product->get_cross_sell_ids();

    if (!empty($upsell_ids) || !empty($crosssell_ids)) {
        $has_additional = true;
    } else {
        // Check if there are related products
        $related_ids = wc_get_related_products($product_id, 5);
        if (!empty($related_ids)) {
            $has_additional = true;
        }
    }

    wp_send_json_success(array(
        'title' => $product->get_name(),
        'image' => $image_url,
        'price' => $product->get_price_html(),
        'attributes' => $attributes,
        'variations' => $variations,
        'has_additional_products' => $has_additional
    ));

    wp_die();
}

/**
 * AJAX handler to get additional/related products
 */
add_action('wp_ajax_get_additional_products', 'customshop_ajax_get_additional_products');
add_action('wp_ajax_nopriv_get_additional_products', 'customshop_ajax_get_additional_products');

function customshop_ajax_get_additional_products() {
    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

    $products_data = array();
    $cart_contents = WC()->cart->get_cart();
    $cart_product_ids = array();

    // Get IDs of products in cart
    foreach ($cart_contents as $cart_item) {
        $cart_product_ids[] = $cart_item['product_id'];
    }

    if ($product_id > 0) {
        // Get upsells, cross-sells and related products for specific product
        $product = wc_get_product($product_id);
        if ($product) {
            $product_ids = array();

            // First priority: Upsells (set in admin)
            $upsell_ids = $product->get_upsell_ids();
            if (!empty($upsell_ids)) {
                $product_ids = array_merge($product_ids, $upsell_ids);
            }

            // Second priority: Cross-sells (set in admin)
            $crosssell_ids = $product->get_cross_sell_ids();
            if (!empty($crosssell_ids)) {
                $product_ids = array_merge($product_ids, $crosssell_ids);
            }

            // Third priority: Related products (automatic by category/tags)
            $related_ids = wc_get_related_products($product_id, 15);
            if (!empty($related_ids)) {
                $product_ids = array_merge($product_ids, $related_ids);
            }

            // Remove duplicates and limit to 15
            $product_ids = array_unique($product_ids);
            $product_ids = array_slice($product_ids, 0, 15);
        } else {
            $product_ids = array();
        }
    } else {
        // Get general popular/featured products
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 15,
            'orderby' => 'rand',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);
        $product_ids = wp_list_pluck($query->posts, 'ID');
    }

    foreach ($product_ids as $id) {
        $product = wc_get_product($id);

        if (!$product || !$product->is_visible()) {
            continue;
        }

        $image_id = $product->get_image_id();
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail') : wc_placeholder_img_src();

        $product_item = array(
            'id' => $product->get_id(),
            'title' => $product->get_name(),
            'url' => get_permalink($product->get_id()),
            'image' => $image_url,
            'price' => $product->get_price_html(),
            'in_cart' => in_array($product->get_id(), $cart_product_ids),
            'is_variable' => $product->is_type('variable'),
            'variations' => array()
        );

        // If variable product, get variations
        if ($product->is_type('variable')) {
            $available_variations = $product->get_available_variations();

            foreach ($available_variations as $variation) {
                $variation_obj = wc_get_product($variation['variation_id']);
                $attributes = array();

                foreach ($variation['attributes'] as $attr_name => $attr_value) {
                    $taxonomy = str_replace('attribute_', '', $attr_name);
                    $term = get_term_by('slug', $attr_value, $taxonomy);
                    $label_name = wc_attribute_label($taxonomy);

                    $attributes[$label_name] = array(
                        'name' => $label_name,
                        'value' => $term ? $term->name : $attr_value,
                        'slug' => $attr_value
                    );
                }

                $product_item['variations'][] = array(
                    'variation_id' => $variation['variation_id'],
                    'attributes' => $attributes,
                    'price' => $variation_obj->get_price_html(),
                    'is_in_stock' => $variation['is_in_stock']
                );
            }
        }

        $products_data[] = $product_item;
    }

    wp_send_json_success(array(
        'products' => $products_data
    ));

    wp_die();
}
