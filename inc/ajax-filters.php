<?php
/**
 * AJAX Shop Filtering and Sorting
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * AJAX обработчик для фильтрации/сортировки товаров
 */
add_action('wp_ajax_filter_products', 'customshop_ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'customshop_ajax_filter_products');

function customshop_ajax_filter_products() {
    // Устанавливаем query vars из AJAX запроса
    if (isset($_POST['orderby'])) {
        $_GET['orderby'] = sanitize_text_field($_POST['orderby']);
    }

    if (isset($_POST['paged'])) {
        set_query_var('paged', intval($_POST['paged']));
    }

    // Подготавливаем query
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => get_option('posts_per_page'),
        'paged'          => isset($_POST['paged']) ? intval($_POST['paged']) : 1,
    );

    // Сортировка
    if (isset($_POST['orderby'])) {
        switch ($_POST['orderby']) {
            case 'popularity':
                $args['meta_key'] = 'total_sales';
                $args['orderby']  = 'meta_value_num';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby']  = 'meta_value_num';
                break;
            case 'date':
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price-desc':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            default:
                $args['orderby'] = 'menu_order title';
                break;
        }
    }

    // Категория
    if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_POST['product_cat']),
            ),
        );
    }

    // Query
    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        ?>
        <div class="products">
            <?php
            while ($query->have_posts()) {
                $query->the_post();
                wc_get_template_part('content', 'product');
            }
            ?>
        </div>
        <?php

        // Пагинация
        if ($query->max_num_pages > 1) {
            $current_page = max(1, $query->query_vars['paged']);
            ?>
            <div class="pagination">
                <div class="pagination__numbers">
                    <?php
                    echo paginate_links(array(
                        'base'      => esc_url_raw(str_replace(999999999, '%#%', get_pagenum_link(999999999))),
                        'format'    => '?paged=%#%',
                        'current'   => $current_page,
                        'total'     => $query->max_num_pages,
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
                    <?php if ($current_page < $query->max_num_pages) : ?>
                        <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="woocommerce-info">' . __('No products found', 'woocommerce') . '</p>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $html,
    ));
}
