<?php
/**
 * Template Name: All Categories
 * Template for displaying all product categories
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<section class="categories">
    <div class="container">
        <div class="section__title">
            <h2><?php _e('Categories', 'kerning-geoshop'); ?></h2>
        </div>
        <div class="cats">
            <?php
            $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'parent'     => 0,
                'orderby'    => 'term_id',
                'order'      => 'ASC'
            ));

            if (!empty($categories) && !is_wp_error($categories)) :
                foreach ($categories as $category) :
                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : get_template_directory_uri() . '/assets/img/item-1.png';
            ?>
                <a href="<?php echo esc_url(get_term_link($category)); ?>" class="cats__item">
                    <h4><?php echo esc_html($category->name); ?></h4>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                </a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<?php
get_footer();
