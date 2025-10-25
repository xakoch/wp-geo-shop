<?php
/**
 * The Template for displaying product archives
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) exit;

get_header('shop');
?>

	<section class="catalog__head">
		<div class="catalog__head-title">
			<?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
				<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
			<?php endif; ?>
		</div>
		
		<div class="catalog__head-filter">
			<p class="woocommerce-result-count">
				<?php
					$total = wc_get_loop_prop('total');
					$per_page = wc_get_loop_prop('per_page');
					$current = wc_get_loop_prop('current_page');

					if ($total <= $per_page || -1 === $per_page) {
						printf(_n('Showing all %d result', 'Showing all %d results', $total, 'kerning-geoshop'), $total);
					} else {
						$first = ($per_page * $current) - $per_page + 1;
						$last = min($total, $per_page * $current);
						printf(_nx('Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'kerning-geoshop'), $first, $last, $total);
					}
				?>
			</p>
			<form class="woocommerce-ordering" method="get">
				<select name="orderby" class="orderby" aria-label="Shop order">
					<?php
					$catalog_orderby_options = apply_filters('woocommerce_catalog_orderby', array(
						'menu_order' => __('Default sorting', 'woocommerce'),
						'popularity' => __('Sort by popularity', 'woocommerce'),
						'rating'     => __('Sort by average rating', 'woocommerce'),
						'date'       => __('Sort by latest', 'woocommerce'),
						'price'      => __('Sort by price: low to high', 'woocommerce'),
						'price-desc' => __('Sort by price: high to low', 'woocommerce'),
					));
					$orderby = isset($_GET['orderby']) ? wc_clean(wp_unslash($_GET['orderby'])) : wc_get_loop_prop('orderby');
					foreach ($catalog_orderby_options as $id => $name) :
					?>
						<option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="paged" value="1">
			</form>
		</div>
	</section>

	<section class="catalog">
		<div class="container">
			<?php
			if (woocommerce_product_loop()) :
			?>
				<div class="products">
					<?php
					if (wc_get_loop_prop('total')) {
						while (have_posts()) {
							the_post();
							do_action('woocommerce_shop_loop');
							wc_get_template_part('content', 'product');
						}
					}
					?>
				</div>
				<?php
				// Пагинация
				$total_pages = wc_get_loop_prop('total_pages');
				$current_page = wc_get_loop_prop('current_page');

				if ($total_pages > 1) :
				?>
					<div class="pagination">
						<div class="pagination__numbers">
							<?php
							echo paginate_links(array(
								'base'      => esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))),
								'format'    => '',
								'current'   => max(1, $current_page),
								'total'     => $total_pages,
								'prev_text' => '',
								'next_text' => '',
								'type'      => 'list',
								'end_size'  => 3,
								'mid_size'  => 3,
							));
							?>
						</div>
						<div class="pagination__arrows">
							<?php if ($current_page > 1) : ?>
								<a href="<?php echo esc_url(get_pagenum_link($current_page - 1)); ?>">Previous</a>
							<?php endif; ?>
							<?php if ($current_page < $total_pages) : ?>
								<a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>">Next</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php
			else :
				do_action('woocommerce_no_products_found');
			endif;
			?>
		</div>
	</section>

<?php
get_footer('shop');