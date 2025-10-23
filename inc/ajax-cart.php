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
 * AJAX обработчик для добавления в корзину со страницы товара
 */
add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'customshop_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'customshop_ajax_add_to_cart');

function customshop_ajax_add_to_cart() {
    // Проверяем, что это AJAX запрос и данные существуют
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Missing product_id');
        wp_die();
    }

    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX::get_refreshed_fragments();
    } else {
        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
        );

        wp_send_json($data);
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
    $fragments['.mini-cart-body'] = '<div class="mini-cart-body">' . ob_get_clean() . '</div>';

    return $fragments;
}

/**
 * AJAX handler to remove item from cart
 */
add_action('wp_ajax_remove_cart_item', 'customshop_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'customshop_ajax_remove_cart_item');

function customshop_ajax_remove_cart_item() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'Missing product_id'));
        wp_die();
    }

    $product_id = absint($_POST['product_id']);
    $cart = WC()->cart->get_cart();
    $removed = false;

    foreach ($cart as $cart_item_key => $cart_item) {
        // Check both product_id and variation_id (in case it's a variation)
        if ($cart_item['product_id'] == $product_id ||
            (isset($cart_item['variation_id']) && $cart_item['variation_id'] == $product_id)) {
            WC()->cart->remove_cart_item($cart_item_key);
            $removed = true;
            break;
        }
    }

    if ($removed) {
        WC()->cart->calculate_totals();

        // Get fragments manually
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();

        $data = array(
            'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array(
                'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
            )),
            'cart_hash' => WC()->cart->get_cart_hash()
        );

        wp_send_json_success($data);
    } else {
        wp_send_json_error(array('message' => 'Product not found in cart'));
    }

    wp_die();
}
