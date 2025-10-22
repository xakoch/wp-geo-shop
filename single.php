<?php
/**
 * Single Post Template
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        while (have_posts()) :
            the_post();
            ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                    
                    <div class="entry-meta">
                        <span class="posted-on">
                            <?php echo get_the_date(); ?>
                        </span>
                        <span class="byline">
                            <?php echo ' | Автор: ' . get_the_author(); ?>
                        </span>
                        <?php if (has_category()) : ?>
                        <span class="cat-links">
                            <?php echo ' | Рубрика: '; the_category(', '); ?>
                        </span>
                        <?php endif; ?>
                    </div><!-- .entry-meta -->
                </header><!-- .entry-header -->

                <?php if (has_post_thumbnail()) : ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Страницы:', 'customshop'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div><!-- .entry-content -->

                <footer class="entry-footer">
                    <?php
                    if (has_tag()) :
                        the_tags('<span class="tags-links">Метки: ', ', ', '</span>');
                    endif;
                    ?>
                </footer><!-- .entry-footer -->
                
            </article><!-- #post-<?php the_ID(); ?> -->

            <?php
            // Навигация между постами
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Предыдущая запись:', 'customshop') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Следующая запись:', 'customshop') . '</span> <span class="nav-title">%title</span>',
            ));

            // Комментарии
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

        endwhile;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();