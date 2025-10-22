<?php
/**
 * Header Template
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, viewport-fit=cover" />
    <meta name="msapplication-TileColor" content="#176DAA">
    <meta name="theme-color" content="#176DAA" />
    <meta name="format-detection" content="telephone=no" />

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="main-wrap">
    <!-- header -->
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <a class="header__logo" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php if (has_custom_logo()) :
                        $custom_logo_id = get_theme_mod('custom_logo');
                        $logo_url = wp_get_attachment_image_src($custom_logo_id, 'full');
                        if ($logo_url) : ?>
                            <img src="<?php echo esc_url($logo_url[0]); ?>" alt="<?php bloginfo('name'); ?>">
                        <?php endif;
                    else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.svg" alt="Geo sidemount diving">
                    <?php endif; ?>
                </a>
                <div class="header__action">
                    <?php if (function_exists('qtranxf_getLanguage')) : ?>
                    <div class="header__lang lang-dropdown">
                        <a class="header__lang-item" href="#">
                            <span><?php echo strtoupper(qtranxf_getLanguage()); ?></span>
                            <svg width="7" height="6" viewBox="0 0 7 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.5 6L0.468911 0.75L6.53109 0.75L3.5 6Z" fill="black" />
                            </svg>
                        </a>
                        <?php echo qtranxf_generateLanguageSelectCode('both'); ?>
                    </div>
                    <?php endif; ?>
                    <a class="header__left--link" href="#contact">
                        <span><?php _e('Services', 'kerning-geoshop'); ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.2021 11.2002H11.8018V5.18848L3.70703 13.2832L2.71875 12.2949L10.8135 4.2002H4.80176V2.7998H13.2021V11.2002Z" fill="#176DAA"/></svg>
                    </a>
                </div>
            </div>

            <!-- middle -->
            <div class="header__middle">
                <ul class="header__menu">
                    <li><a href="/categories">Products</a></li>
                    <li><a href="#contact"><?php _e('Contact', 'kerning-geoshop'); ?></a></li>
                </ul>
                <button class="burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <!-- right -->
            <div class="header__right">
                <?php if (get_option('users_can_register')) : ?>
                <a href="#" class="account-link">
                    <?php if (is_user_logged_in()) :
                        $current_user = wp_get_current_user();
                        echo esc_html($current_user->display_name);
                    else :
                        _e('Login', 'kerning-geoshop');
                    endif; ?>
                </a>
                <?php endif; ?>
                <?php if (function_exists('WC')) : ?>
                <a href="#" class="cart cart-link">
                    <span class="cart-text"><?php _e('Cart', 'kerning-geoshop'); ?></span>
                    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <!-- /header -->

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="mobile-menu__content">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'mobile',
                'menu_class'     => 'mobile-menu__menu',
                'container'      => false,
                'fallback_cb'    => false,
            ));
            ?>
            <div class="mobile-menu__action">
                <a class="header__call" href="tel:+37123111390">+371 23 111 390</a>
                <a class="header__cta" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php _e('Shop', 'kerning-geoshop'); ?></a>
                <?php if (function_exists('qtranxf_generateLanguageSelectCode')) : ?>
                <div class="mobile-menu__lang">
                    <?php echo qtranxf_generateLanguageSelectCode('text'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- /Mobile Menu -->