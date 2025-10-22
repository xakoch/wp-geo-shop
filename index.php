<?php
/**
 * Main Template
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        if (have_posts()) :

            while (have_posts()) :
                the_post();
                ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    
                    <header class="entry-header">
                        <?php
                        if (is_singular()) :
                            the_title('<h1 class="entry-title">', '</h1>');
                        else :
                            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                        endif;
                        ?>
                        
                        <?php if ('post' === get_post_type()) : ?>
                        <div class="entry-meta">
                            <span class="posted-on">
                                <?php echo get_the_date(); ?>
                            </span>
                            <span class="byline">
                                <?php echo ' | ' . get_the_author(); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </header><!-- .entry-header -->

                    <?php if (has_post_thumbnail() && !is_singular()) : ?>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php
                        if (is_singular()) :
                            the_content();
                        else :
                            the_excerpt();
                        endif;
                        
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Страницы:', 'customshop'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div><!-- .entry-content -->

                    <?php if (!is_singular()) : ?>
                    <footer class="entry-footer">
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php esc_html_e('Читать далее', 'customshop'); ?> &rarr;
                        </a>
                    </footer>
                    <?php endif; ?>
                    
                </article><!-- #post-<?php the_ID(); ?> -->

                <?php
            endwhile;

            // Пагинация
            the_posts_pagination(array(
                'mid_size'  => 2,
                'prev_text' => __('&laquo; Назад', 'customshop'),
                'next_text' => __('Вперёд &raquo;', 'customshop'),
            ));

        else :

            get_template_part('template-parts/content', 'none');

        endif;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();