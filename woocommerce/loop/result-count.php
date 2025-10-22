<?php
/**
 * Result Count
 *
 * @package kerning-geoshop
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<p class="woocommerce-result-count">
    <?php
    $paged    = max(1, $current);
    $per_page = $per_page;
    $total    = $total;
    $first    = ($per_page * $paged) - $per_page + 1;
    $last     = min($total, $per_page * $paged);

    if ($total <= $per_page || -1 === $per_page) {
        printf(_n('Showing all %d result', 'Showing all %d results', $total, 'kerning-geoshop'), $total);
    } else {
        printf(_nx('Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'kerning-geoshop'), $first, $last, $total);
    }
    ?>
</p>
