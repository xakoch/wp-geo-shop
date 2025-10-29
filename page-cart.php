<?php
/**
 * Template name: Cart Page
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<?php
// Просто выводим содержимое страницы
// WooCommerce сам загрузит свой шаблон корзины через шорткод [woocommerce_cart]
while (have_posts()) {
    the_post();
    the_content();
}
?>

<?php
get_footer();
?>