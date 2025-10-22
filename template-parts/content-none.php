<?php
/**
 * Template part for displaying a message when no content is found
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;
?>

<section class="no-results not-found">
    
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Ничего не найдено', 'customshop'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :
            ?>
            <p>
                <?php
                printf(
                    wp_kses(
                        __('Готовы опубликовать первую запись? <a href="%1$s">Начните здесь</a>.', 'customshop'),
                        array(
                            'a' => array(
                                'href' => array(),
                            ),
                        )
                    ),
                    esc_url(admin_url('post-new.php'))
                );
                ?>
            </p>
            <?php
        elseif (is_search()) :
            ?>
            <p><?php esc_html_e('К сожалению, по вашему запросу ничего не найдено. Попробуйте другие ключевые слова.', 'customshop'); ?></p>
            <?php
            get_search_form();
        else :
            ?>
            <p><?php esc_html_e('Похоже, здесь ничего нет. Попробуйте воспользоваться поиском.', 'customshop'); ?></p>
            <?php
            get_search_form();
        endif;
        ?>
    </div><!-- .page-content -->
    
</section><!-- .no-results -->