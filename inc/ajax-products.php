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

    // Get product attributes with proper formatting
    $attributes = array();
    $available_variations = $product->get_available_variations();

    foreach ($product->get_variation_attributes() as $attribute_name => $options) {
        $attribute_label = wc_attribute_label($attribute_name);
        $taxonomy = str_replace('pa_', '', $attribute_name);

        // Format options with color codes for color attributes
        $formatted_options = array();

        foreach ($options as $option_slug) {
            $term = get_term_by('slug', $option_slug, $attribute_name);

            $option_data = array(
                'name' => $term ? $term->name : $option_slug,
                'slug' => $option_slug
            );

            // Get color code if this is a color attribute
            if (strpos($attribute_name, 'color') !== false || strpos($attribute_name, 'pa_color') !== false) {
                $color = '';

                // Try to get color from term meta
                if ($term) {
                    $color = get_term_meta($term->term_id, 'color', true);
                }

                // If no color in meta, generate from color name
                if (empty($color)) {
                    $color = customshop_get_color_from_name($term ? $term->name : $option_slug);
                }

                $option_data['color'] = $color;
            }

            $formatted_options[] = $option_data;
        }

        $attributes[$attribute_name] = $formatted_options;
    }

    // Format variations
    $variations = array();
    foreach ($available_variations as $variation) {
        $variation_obj = wc_get_product($variation['variation_id']);

        $variations[] = array(
            'variation_id' => $variation['variation_id'],
            'attributes' => $variation['attributes'],
            'price_html' => $variation_obj->get_price_html(),
            'is_in_stock' => $variation['is_in_stock'],
            'sku' => $variation_obj->get_sku(),
            'max_qty' => $variation_obj->get_stock_quantity() ? $variation_obj->get_stock_quantity() : '',
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

/**
 * Get color hex code from color name
 *
 * @param string $color_name Color name (e.g., "Red", "Blue", "Black")
 * @return string Hex color code
 */
function customshop_get_color_from_name($color_name) {
    $color_name = strtolower(trim($color_name));

    // Common color mappings
    $color_map = array(
        // Basic colors
        'black' => '#000000',
        'white' => '#FFFFFF',
        'red' => '#FF0000',
        'blue' => '#0000FF',
        'green' => '#008000',
        'yellow' => '#FFFF00',
        'orange' => '#FFA500',
        'purple' => '#800080',
        'pink' => '#FFC0CB',
        'brown' => '#8B4513',
        'gray' => '#808080',
        'grey' => '#808080',

        // Extended colors
        'navy' => '#000080',
        'teal' => '#008080',
        'lime' => '#00FF00',
        'aqua' => '#00FFFF',
        'maroon' => '#800000',
        'olive' => '#808000',
        'silver' => '#C0C0C0',
        'gold' => '#FFD700',
        'beige' => '#F5F5DC',
        'tan' => '#D2B48C',
        'khaki' => '#F0E68C',
        'cyan' => '#00FFFF',
        'magenta' => '#FF00FF',
        'violet' => '#EE82EE',
        'indigo' => '#4B0082',
        'turquoise' => '#40E0D0',
        'coral' => '#FF7F50',
        'salmon' => '#FA8072',
        'crimson' => '#DC143C',
        'chocolate' => '#D2691E',

        // Shades
        'dark blue' => '#00008B',
        'light blue' => '#ADD8E6',
        'dark green' => '#006400',
        'light green' => '#90EE90',
        'dark red' => '#8B0000',
        'dark gray' => '#A9A9A9',
        'light gray' => '#D3D3D3',
        'dark grey' => '#A9A9A9',
        'light grey' => '#D3D3D3',

        // Other common names
        'cream' => '#FFFDD0',
        'ivory' => '#FFFFF0',
        'mint' => '#98FF98',
        'lavender' => '#E6E6FA',
        'peach' => '#FFE5B4',
        'burgundy' => '#800020',
        'mustard' => '#FFDB58',
        'charcoal' => '#36454F',
    );

    // Check if color exists in map
    if (isset($color_map[$color_name])) {
        return $color_map[$color_name];
    }

    // Try to match partial names
    foreach ($color_map as $name => $hex) {
        if (strpos($color_name, $name) !== false || strpos($name, $color_name) !== false) {
            return $hex;
        }
    }

    // Default fallback - generate from string hash
    return '#' . substr(md5($color_name), 0, 6);
}
