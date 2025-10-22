<?php
/**
 * The Template for displaying single product
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

get_header('shop');
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        /**
         * Hook: woocommerce_before_main_content
         */
        do_action('woocommerce_before_main_content');
        ?>

        <?php while (have_posts()) : ?>
            <?php the_post(); ?>

            <?php wc_get_template_part('content', 'single-product'); ?>

        <?php endwhile; ?>

        <?php
        /**
         * Hook: woocommerce_after_main_content
         */
        do_action('woocommerce_after_main_content');
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar('shop');
get_footer('shop');