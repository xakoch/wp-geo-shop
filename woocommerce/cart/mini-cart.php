<?php
/**
 * Mini Cart Template
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) exit;

do_action('woocommerce_before_mini_cart'); ?>

<?php if (!WC()->cart->is_empty()) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget">
		<?php
		do_action('woocommerce_before_mini_cart_contents');

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
			$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
				$product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
				$thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
				$product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
				$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
				?>
				<li class="woocommerce-mini-cart-item mini_cart_item">
					<div class="mini-cart-item-image">
						<?php if (empty($product_permalink)) : ?>
							<?php echo $thumbnail; ?>
						<?php else : ?>
							<a href="<?php echo esc_url($product_permalink); ?>">
								<?php echo $thumbnail; ?>
							</a>
						<?php endif; ?>
					</div>
					<div class="mini-cart-item-details">
						<div class="mini-cart-item-details-top">
							<div class="mini-cart-item-name">
								<?php if (empty($product_permalink)) : ?>
									<?php echo wp_kses_post($product_name); ?>
								<?php else : ?>
									<a href="<?php echo esc_url($product_permalink); ?>">
										<?php echo wp_kses_post($product_name); ?>
									</a>
								<?php endif; ?>
							</div>
							<div class="mini-cart-item-pricing">
								<div class="mini-cart-item-price">
									<span class="price-value">
										<?php echo apply_filters('woocommerce_cart_item_subtotal', wc_price($cart_item['line_total'] + $cart_item['line_tax']), $cart_item, $cart_item_key); ?>
									</span>
								</div>
							</div>
						</div>
						<div class="mini-cart-item-details-bottom">
							<?php
							echo apply_filters(
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 4H13V14C13 14.2652 12.8946 14.5195 12.707 14.707C12.5195 14.8946 12.2652 15 12 15H4C3.73478 15 3.48051 14.8946 3.29297 14.707C3.10543 14.5195 3 14.2652 3 14V4H2V3H14V4ZM4 14H12V4H4V14ZM7 12H6V6H7V12ZM10 12H9V6H10V12ZM10 1V2H6V1H10Z" fill="#F52222"/></svg><span>Remove</span></a>',
									esc_url(wc_get_cart_remove_url($cart_item_key)),
									esc_attr__('Remove this item', 'woocommerce'),
									esc_attr($product_id),
									esc_attr($cart_item_key),
									esc_attr($_product->get_sku())
								),
								$cart_item_key
							);
							?>
						</div>
					</div>
				</li>
				<?php
			}
		}

		do_action('woocommerce_mini_cart_contents');
		?>
	</ul>
	<div class="mini-cart-totals">
		<div class="mini-cart-summary">
			<div class="summary-row">
				<span class="summary-label"><?php _e('Items in cart:', 'kerning-geoshop'); ?></span>
				<span class="summary-value"><?php echo WC()->cart->get_cart_contents_count(); ?> <?php _e('pc', 'kerning-geoshop'); ?></span>
			</div>
			<div class="summary-row">
				<span class="summary-label"><?php _e('Interim result:', 'kerning-geoshop'); ?></span>
				<span class="summary-value"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
			</div>
		</div>

		<div class="woocommerce-mini-cart__total total">
			<strong><?php _e('Total:', 'kerning-geoshop'); ?></strong>
			<strong class="total-amount"><?php echo WC()->cart->get_total(); ?></strong>
		</div>
	</div>
	<div class="woocommerce-mini-cart__buttons buttons">
		<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="button wc-forward"><?php _e('Cart', 'kerning-geoshop'); ?></a>
		<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button checkout wc-forward"><?php _e('Place an order', 'kerning-geoshop'); ?></a>
	</div>

<?php else : ?>

	<div class="woocommerce-mini-cart__empty-message">
		<svg class="empty-cart-icon" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M20 16L24 8H40L44 16M20 16H8L12 48H52L56 16H44M20 16H44M28 24V40M36 24V40" stroke="#D1D5DB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<p class="empty-cart-text"><?php _e('The basket is empty', 'kerning-geoshop'); ?></p>
		<p class="empty-cart-subtext"><?php _e('Add products to get started', 'kerning-geoshop'); ?></p>
	</div>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>