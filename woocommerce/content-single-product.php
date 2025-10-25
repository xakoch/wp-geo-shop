<?php
/**
 * The template for displaying single product content
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

global $product;

if (!$product) {
    return;
}

// Get product data
$product_image_id = $product->get_image_id();
$product_gallery_ids = $product->get_gallery_image_ids();
$main_image = wp_get_attachment_image_url($product_image_id, 'full');
$price = $product->get_price_html();
$sku = $product->get_sku();
?>

<div class="product">
	<div class="product-gallery">

		<!-- Главное изображение -->
		<div class="product-gallery__main">
			<a href="<?php echo esc_url($main_image); ?>" data-fancybox="gallery">
				<img src="<?php echo esc_url($main_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
			</a>
		</div>

		<!-- Сетка изображений (2 колонки) -->
		<?php if (!empty($product_gallery_ids)) : ?>
		<div class="product-gallery__grid">
			<?php
			$gallery_count = 0;
			foreach ($product_gallery_ids as $gallery_image_id) :
			    if ($gallery_count >= 3) break;
			    $gallery_image = wp_get_attachment_image_url($gallery_image_id, 'full');
			?>
			<a href="<?php echo esc_url($gallery_image); ?>" data-fancybox="gallery" class="product-gallery__item">
				<img src="<?php echo esc_url($gallery_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
			</a>
			<?php
			$gallery_count++;
			endforeach;
			?>
		</div>
		<?php endif; ?>
	</div>

	<div class="product-info">
		<form class="cart" method="post" enctype="multipart/form-data" data-product-id="<?php echo esc_attr($product->get_id()); ?>" data-product-type="<?php echo esc_attr($product->get_type()); ?>">
			<h1 class="product-info__title"><?php the_title(); ?></h1>

			<div class="product-info__price"><?php echo $price; ?></div>

			<?php if ($product->is_type('variable')) :
				$attributes = $product->get_variation_attributes();
				$has_colors = isset($attributes['pa_color']) && !empty($attributes['pa_color']);
				$has_sizes = isset($attributes['pa_size']) && !empty($attributes['pa_size']);
			?>
			<!-- Цвета -->
			<?php if ($has_colors) : ?>
			<div class="product-info__colors">
				<label class="product-info__label"><?php _e('Color', 'kerning-geoshop'); ?></label>
				<div class="product-info__color-list" data-attribute-name="pa_color">
					<!-- Colors will be populated dynamically -->
				</div>
			</div>
			<?php endif; ?>

			<!-- Размеры -->
			<?php if ($has_sizes) : ?>
			<div class="product-info__sizes">
				<label class="product-info__label"><?php _e('Size', 'kerning-geoshop'); ?></label>
				<div class="product-info__size-list" data-attribute-name="pa_size">
					<!-- Sizes will be populated dynamically -->
				</div>
			</div>
			<?php endif; ?>

			<!-- Hidden inputs for variations -->
			<input type="hidden" name="variation_id" class="variation_id" value="0" />
			<input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>" />
			<?php else : ?>
			<!-- Hidden input for simple products -->
			<input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>" />
			<?php endif; ?>

			<!-- Количество -->
			<div class="product-info__quantity-block">
				<label class="product-info__label"><?php _e('Amount', 'kerning-geoshop'); ?></label>
				<div class="product-info__quantity">
					<button type="button" class="qty-minus">-</button>
					<input type="number" name="quantity" value="1" min="1" class="qty" />
					<button type="button" class="qty-plus">+</button>
				</div>
			</div>

			<!-- Кнопки действий -->
			<div class="product-info__actions">
				<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="btn btn--secondary single_add_to_cart_button"><?php _e('Add to cart', 'kerning-geoshop'); ?></button>
				<button type="button" class="btn btn--primary buy-now-button"><?php _e('Buy now', 'kerning-geoshop'); ?></button>
			</div>

			<!-- SKU -->
			<?php if ($sku) : ?>
			<div class="product-info__sku">
				<span class="product-info__sku-label"><?php _e('SKU:', 'kerning-geoshop'); ?></span>
				<span class="product-info__sku-value"><?php echo esc_html($sku); ?></span>
			</div>
			<?php endif; ?>
		</form>
	</div>
</div>

<div class="product-description">
	<div class="product-description__container">
		<h2 class="product-description__title"><?php _e('Description', 'kerning-geoshop'); ?></h2>
		<div class="product-description__content">
			<?php the_content(); ?>
		</div>
	</div>
</div>

<!-- You may also like section -->
<?php
// Get related products
$related_ids = wc_get_related_products($product->get_id(), 5);
if (!empty($related_ids)) :
?>
<section class="recommendations">
	<div class="recommendations__container">
		<h2 class="recommendations__title"><?php _e('You may also like...', 'kerning-geoshop'); ?></h2>
		<div class="products">
			<?php
			foreach ($related_ids as $related_id) :
			    $related_product = wc_get_product($related_id);
			    if (!$related_product) continue;

			    $related_image_id = $related_product->get_image_id();
			    $related_image = wp_get_attachment_image_url($related_image_id, 'medium');
			    $related_price = $related_product->get_price_html();
			    $related_regular_price = $related_product->get_regular_price();
			    $related_sale_price = $related_product->get_sale_price();
			    $is_on_sale = $related_product->is_on_sale();

			    // Calculate discount percentage
			    $discount_percent = '';
			    if ($is_on_sale && $related_regular_price) {
			        $discount = (($related_regular_price - $related_sale_price) / $related_regular_price) * 100;
			        $discount_percent = '-' . round($discount) . '%';
			    }

			    // Get variation colors count (if variable product)
			    $colors_count = '';
			    if ($related_product->is_type('variable')) {
			        $variations = $related_product->get_available_variations();
			        $colors_count = count($variations) . ' ' . __('colors', 'kerning-geoshop');
			    }
			?>
			<div class="product">
				<?php if ($colors_count) : ?>
				<span class="colors-count"><?php echo esc_html($colors_count); ?></span>
				<?php endif; ?>
				<?php if ($discount_percent) : ?>
				<span class="sale-percent"><?php echo esc_html($discount_percent); ?></span>
				<?php endif; ?>
				<div class="product__img">
					<a href="<?php echo get_permalink($related_id); ?>">
						<img src="<?php echo esc_url($related_image); ?>" alt="<?php echo esc_attr($related_product->get_name()); ?>">
					</a>
				</div>
				<div class="product__footer">
					<div class="product__footer-info">
						<div class="product__title">
							<a href="<?php echo get_permalink($related_id); ?>">
								<h3><?php echo esc_html($related_product->get_name()); ?></h3>
							</a>
						</div>
						<div class="product__price">
							<?php echo $related_price; ?>
						</div>
					</div>
					<div class="product__footer-action">
						<button class="add-to-cart" data-product-id="<?php echo esc_attr($related_id); ?>">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.8574 0V9.14258H20V10.8574H10.8574V20H9.14258V10.8574H0V9.14258H9.14258V0H10.8574Z" fill="#176DAA"/></svg>
						</button>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>
