<?php
/**
 * AJAX Cart Functionality
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Обеспечиваем, что AJAX добавление в корзину включено на страницах архивов
 */
function customshop_ensure_ajax_add_to_cart_is_enabled() {
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        wp_enqueue_script('wc-add-to-cart');
    }
}
add_action('wp_enqueue_scripts', 'customshop_ensure_ajax_add_to_cart_is_enabled');

/**
 * Включаем AJAX добавление в корзину для простых товаров на странице товара
 */
add_filter('woocommerce_product_single_add_to_cart_text', 'customshop_single_product_ajax_add_to_cart');
function customshop_single_product_ajax_add_to_cart($text) {
    global $product;

    // Проверяем, что объект продукта существует
    if (!$product || !is_object($product)) {
        return $text;
    }

    return __('Добавить в корзину', 'woocommerce');
}

/**
 * Добавляем класс для AJAX кнопки на странице товара
 */
add_filter('woocommerce_loop_add_to_cart_link', 'customshop_add_ajax_class_to_single_product', 10, 3);
function customshop_add_ajax_class_to_single_product($html, $product, $args) {
    // Проверяем, что объект продукта существует
    if (!$product || !is_object($product) || !method_exists($product, 'is_type')) {
        return $html;
    }

    // Если это простой товар, добавляем AJAX класс
    if ($product->is_type('simple')) {
        $html = str_replace('add_to_cart_button', 'add_to_cart_button ajax_add_to_cart', $html);
    }
    return $html;
}

/**
 * AJAX handler for adding products to cart
 */
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'customshop_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'customshop_ajax_add_to_cart');

function customshop_ajax_add_to_cart() {
    // Проверяем, что это AJAX запрос и данные существуют
    if (!isset($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Missing product_id'));
        return;
    }

    // DEBUG: Log what we receive
    error_log('========== ADD TO CART DEBUG ==========');
    error_log('POST keys: ' . implode(', ', array_keys($_POST)));
    error_log('variation_id in POST: ' . (isset($_POST['variation_id']) ? $_POST['variation_id'] : 'NOT SET'));
    error_log('Full POST: ' . print_r($_POST, true));

    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;

    error_log('After parsing - variation_id: ' . $variation_id);
    error_log('=======================================');

    // Get variation attributes if this is a variable product
    $variation = array();
    if ($variation_id > 0) {
        $variation_product = wc_get_product($variation_id);
        if ($variation_product) {
            $variation = $variation_product->get_variation_attributes();
        }
    } else {
        // Try to get attributes from POST data
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'attribute_') === 0) {
                $variation[$key] = sanitize_text_field($value);
            }
        }
    }

    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);
    $product_status = get_post_status($product_id);

    if (!$passed_validation) {
        wp_send_json_error(array('message' => 'Validation failed'));
        return;
    }

    if ('publish' !== $product_status) {
        wp_send_json_error(array('message' => 'Product is not available'));
        return;
    }

    // Ensure WooCommerce session is started
    if (WC()->session && !WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }

    // Get the product to check its type
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
        return;
    }

    // Check if this is a variable product but no variation selected
    if ($product->is_type('variable') && $variation_id === 0 && empty($variation)) {
        wp_send_json_error(array('message' => 'Please select product options'));
        return;
    }

    // If we have variation attributes but no variation_id, try to find the matching variation
    if ($product->is_type('variable') && $variation_id === 0 && !empty($variation)) {
        $data_store = WC_Data_Store::load('product');
        $variation_id = $data_store->find_matching_product_variation($product, $variation);

        if (!$variation_id) {
            wp_send_json_error(array('message' => 'Could not find matching product variation'));
            return;
        }
    }

    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);

    // If failed, get WooCommerce notices/errors
    if (!$cart_item_key) {
        $notices = wc_get_notices('error');
        wc_clear_notices();

        // Get first error message if available
        $error_message = 'Could not add product to cart';
        if (!empty($notices)) {
            $first_notice = reset($notices);
            if (isset($first_notice['notice'])) {
                $error_message = $first_notice['notice'];
            }
        }

        wp_send_json_error(array('message' => $error_message));
        return;
    }

    if ($cart_item_key) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX::get_refreshed_fragments();
    } else {
        error_log('Add to cart - Failed to add product to cart');
        wp_send_json_error(array('message' => 'Could not add product to cart'));
    }

    wp_die();
}

/**
 * Обновление фрагментов корзины (для AJAX)
 */
add_filter('woocommerce_add_to_cart_fragments', 'customshop_mini_cart_fragments');

function customshop_mini_cart_fragments($fragments) {
    // Обновление счётчика в header
    ob_start();
    ?>
    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();

    // Обновление тела мини-корзины
    ob_start();
    woocommerce_mini_cart();
    $mini_cart_html = ob_get_clean();

    $fragments['.mini-cart-body'] = '<div class="mini-cart-body">' . $mini_cart_html . '</div>';

    // Also add the full widget content for compatibility
    $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart_html . '</div>';

    return $fragments;
}

/**
 * AJAX handler to update cart item quantity
 */
add_action('wp_ajax_update_cart_item_qty', 'customshop_ajax_update_cart_item_qty');
add_action('wp_ajax_nopriv_update_cart_item_qty', 'customshop_ajax_update_cart_item_qty');

function customshop_ajax_update_cart_item_qty() {
    if (!isset($_POST['cart_item_key']) || !isset($_POST['quantity'])) {
        wp_send_json_error(array('message' => 'Missing required parameters'));
        wp_die();
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = absint($_POST['quantity']);

    if ($quantity <= 0) {
        wp_send_json_error(array('message' => 'Invalid quantity'));
        wp_die();
    }

    // Update cart item quantity
    $updated = WC()->cart->set_quantity($cart_item_key, $quantity, true);

    if ($updated) {
        WC()->cart->calculate_totals();

        // Get updated cart data
        $cart_item = WC()->cart->get_cart_item($cart_item_key);
        $product = $cart_item['data'];

        // Get fragments
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $data = array(
            'cart_item_key' => $cart_item_key,
            'quantity' => $quantity,
            'line_total' => WC()->cart->get_product_subtotal($product, $quantity),
            'cart_subtotal' => WC()->cart->get_cart_subtotal(),
            'cart_total' => WC()->cart->get_total(),
            'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
                'span.cart-count' => '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>',
            )),
            'cart_hash' => WC()->cart->get_cart_hash()
        );

        wp_send_json_success($data);
    } else {
        wp_send_json_error(array('message' => 'Failed to update cart'));
    }

    wp_die();
}

/**
 * AJAX handler to remove item from cart
 */
add_action('wp_ajax_remove_cart_item', 'customshop_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'customshop_ajax_remove_cart_item');

function customshop_ajax_remove_cart_item() {
    // Debug logging
    error_log('========== REMOVE CART ITEM DEBUG ==========');
    error_log('POST data: ' . print_r($_POST, true));

    if (!isset($_POST['cart_item_key'])) {
        error_log('ERROR: cart_item_key not set');
        wp_send_json_error(array('message' => 'Missing cart_item_key'));
        return;
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    error_log('Cart item key: ' . $cart_item_key);

    // Ensure WooCommerce session and cart are properly initialized
    if (!WC()->cart) {
        error_log('ERROR: WC()->cart not available');
        wp_send_json_error(array('message' => 'Cart not available'));
        return;
    }

    // Make sure we have the latest cart data from session
    WC()->cart->get_cart_from_session();

    // Get current cart contents
    $cart_contents = WC()->cart->get_cart();
    error_log('Cart contents keys: ' . print_r(array_keys($cart_contents), true));

    // Try to remove item even if not found in current cart state
    // WooCommerce will handle it gracefully
    $removed = WC()->cart->remove_cart_item($cart_item_key);
    error_log('Removed: ' . ($removed ? 'YES' : 'NO'));

    if ($removed) {
        // Recalculate totals
        WC()->cart->calculate_totals();

        // Persist to session
        WC()->cart->persistent_cart_update();

        // Get fragments manually
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $data = array(
            'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
                'span.cart-count' => '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>',
            )),
            'cart_hash' => WC()->cart->get_cart_hash(),
            'cart_is_empty' => WC()->cart->is_empty()
        );

        error_log('Success! Cart is empty: ' . ($data['cart_is_empty'] ? 'YES' : 'NO'));
        error_log('===========================================');
        wp_send_json_success($data);
    } else {
        error_log('ERROR: Failed to remove item - item may not exist');
        error_log('===========================================');
        wp_send_json_error(array('message' => 'Failed to remove item from cart'));
    }
}
