<?php
/**
 * Theme Setup and Configuration
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function customshop_setup() {
    // Поддержка title-tag
    add_theme_support('title-tag');

    // Миниатюры постов
    add_theme_support('post-thumbnails');

    // HTML5 разметка
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));

    // Логотип сайта
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Регистрация меню
    register_nav_menus(array(
        'primary' => 'Основное меню',
        'mobile'  => 'Мобильное меню',
        'footer'  => 'Меню в футере',
    ));
}
add_action('after_setup_theme', 'customshop_setup');
