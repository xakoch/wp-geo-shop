<?php
/**
 * Widget Areas Registration
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Register widget areas
 */
function customshop_widgets_init() {
    register_sidebar(array(
        'name'          => 'Основной сайдбар',
        'id'            => 'sidebar-1',
        'description'   => 'Добавьте виджеты в сайдбар',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'customshop_widgets_init');
