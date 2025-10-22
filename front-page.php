<?php
/**
 * Front Page Template (Home Page)
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) exit;

get_header();

?>

<section class="hero">
    <div class="hero__img">
        <?php
        $banner_image = get_theme_mod('hero_banner_image');
        if ($banner_image) : ?>
            <img src="<?php echo esc_url($banner_image); ?>" alt="<?php bloginfo('name'); ?>">
        <?php else : ?>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/banner.png" alt="<?php bloginfo('name'); ?>">
        <?php endif; ?>
    </div>
    <div class="hero__slider"></div>
</section>

<section class="about">
    <div class="container">
        <div class="about__inner">
            <div class="about__title">
                <h2><?php echo get_theme_mod('about_title', 'GeoDivingShop'); ?></h2>
            </div>
            <div class="about__text">
                <div class="about__text--1">
                    <p><?php echo get_theme_mod('about_text_1', 'GeoDivingShop supplies professional-grade diving equipment for technical divers, commercial use, and research applications.'); ?></p>
                </div>
                <div class="about__text--2">
                    <p><?php echo get_theme_mod('about_text_2', 'Our catalog focuses on reliability, durability, and compliance with industry standards.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="categories">
    <div class="container">
        <div class="section__title">
            <h2><?php _e('Popular categories', 'kerning-geoshop'); ?></h2>
        </div>
        <div class="cats">
            <?php
            $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'parent'     => 0,
                'number'     => 4,
                'order'      => 'DESC'
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

<section class="catalog new-items">
    <div class="container">
        <div class="section__title">
            <h2><?php _e('New items', 'kerning-geoshop'); ?></h2>
        </div>
        <div class="products">
            <?php
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => 8,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $loop = new WP_Query($args);

            if ($loop->have_posts()) :
                while ($loop->have_posts()) : $loop->the_post();
                    global $product;
                    wc_get_template_part('content', 'product');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
        <div class="catalog-btn-viewall">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php _e('View all', 'kerning-geoshop'); ?></a>
        </div>
    </div>
</section>

<section class="howto">
    <div class="howto__img">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/how-to.png" alt="<?php _e('How to Order', 'kerning-geoshop'); ?>">
    </div>
    <div class="howto__info">
        <div class="section__title">
            <h2><?php _e('How to Order', 'kerning-geoshop'); ?></h2>
        </div>
        <ul class="howto__list">
            <li>
                <span>1</span>
                <h4><?php _e('Select product or send request.', 'kerning-geoshop'); ?></h4>
            </li>
            <li>
                <span>2</span>
                <h4><?php _e('Specify technical requirements.', 'kerning-geoshop'); ?></h4>
            </li>
            <li>
                <span>3</span>
                <h4><?php _e('Receive confirmation and delivery details.', 'kerning-geoshop'); ?></h4>
            </li>
        </ul>
    </div>
</section>

<section class="why">
    <div class="container">
        <div class="section__title">
            <h2><?php _e('Why Choose GeoDivingShop', 'kerning-geoshop'); ?></h2>
        </div>
        <div class="why__list">
            <div class="why__item">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/why-1.svg" alt="">
                <div class="why__footer">
                    <h4><?php _e('Field Experience', 'kerning-geoshop'); ?></h4>
                    <p><?php _e('Our team consists of divers with extensive practical background.', 'kerning-geoshop'); ?></p>
                </div>
            </div>
            <div class="why__item">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/why-2.svg" alt="">
                <div class="why__footer">
                    <h4><?php _e('Tested Quality', 'kerning-geoshop'); ?></h4>
                    <p><?php _e('Every product is inspected and tested to ensure performance under demanding conditions.', 'kerning-geoshop'); ?></p>
                </div>
            </div>
            <div class="why__item">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/why-3.svg" alt="">
                <div class="why__footer">
                    <h4><?php _e('Customization', 'kerning-geoshop'); ?></h4>
                    <p><?php _e('Equipment can be adapted to specific operational needs.', 'kerning-geoshop'); ?></p>
                </div>
            </div>
            <div class="why__item">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/why-4.svg" alt="">
                <div class="why__footer">
                    <h4><?php _e('Transparent Pricing', 'kerning-geoshop'); ?></h4>
                    <p><?php _e('No hidden costs, clear conditions for shipping, taxes, and duties.', 'kerning-geoshop'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="services">
    <div class="services__info">
        <div class="section__title">
            <h2><?php _e('Service', 'kerning-geoshop'); ?></h2>
            <p><?php _e('We provide dry suit maintenance', 'kerning-geoshop'); ?> <br> <?php _e('and repair in addition to production.', 'kerning-geoshop'); ?></p>
        </div>
        <p><?php echo get_theme_mod('services_text', 'Our services range from simple seal replacement to resizing and material restoration. All suits undergo hydrostatic testing before work begins, if the condition of the suit allows, to assess cost and feasibility. Consultation is free — contact us for an estimate.'); ?></p>
    </div>
    <div class="services__img">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/services.png" alt="<?php _e('Service', 'kerning-geoshop'); ?>">
    </div>
</section>

<?php get_footer();