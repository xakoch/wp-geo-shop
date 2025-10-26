<?php /* Template name: Checkout */ ?>

<?php get_header(); ?>

	<div class="woo-custom-checkout">
		<div class="container">
			<?= do_shortcode('[woocommerce_checkout]'); ?>
		</div>
	</div>

<?php get_footer(); ?>