<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) {
    exit;
}

$total   = isset($total) ? $total : wc_get_loop_prop('total_pages');
$current = isset($current) ? $current : wc_get_loop_prop('current_page');
$base    = isset($base) ? $base : esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false))));
$format  = isset($format) ? $format : '';

if ($total <= 1) {
    return;
}
?>
<div class="pagination">
    <div class="pagination__numbers">
        <?php
        echo paginate_links(
            apply_filters(
                'woocommerce_pagination_args',
                array(
                    'base'      => $base,
                    'format'    => $format,
                    'add_args'  => false,
                    'current'   => max(1, $current),
                    'total'     => $total,
                    'prev_text' => '',
                    'next_text' => '',
                    'type'      => 'list',
                    'end_size'  => 3,
                    'mid_size'  => 3,
                )
            )
        );
        ?>
    </div>
    <div class="pagination__arrows">
        <?php if ($current > 1) : ?>
            <a href="<?php echo esc_url(get_pagenum_link($current - 1)); ?>"><?php _e('Previous', 'kerning-geoshop'); ?></a>
        <?php endif; ?>
        <?php if ($current < $total) : ?>
            <a href="<?php echo esc_url(get_pagenum_link($current + 1)); ?>"><?php _e('Next', 'kerning-geoshop'); ?></a>
        <?php endif; ?>
    </div>
</div>
