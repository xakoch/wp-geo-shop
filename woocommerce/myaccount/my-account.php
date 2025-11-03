<?php
/**
 * My Account Page
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;
?>

<section class="catalog__head">
    <div class="catalog__head-title">
        <h1 class="page-title"><?php _e('My Account', 'kerning-geoshop'); ?></h1>
    </div>
</section>

<div class="container">
    <?php woocommerce_output_all_notices(); ?>
</div>

<section class="my-account-page">
    <div class="container">
        <div class="my-account__wrapper">
            <div class="my-account__navigation">
                <?php
                /**
                 * Hook: woocommerce_account_navigation
                 * Навигация личного кабинета
                 */
                do_action('woocommerce_account_navigation');
                ?>
            </div>

            <div class="my-account__content">
                <div class="woocommerce-MyAccount-content">
                    <?php
                    /**
                     * Hook: woocommerce_account_content
                     * Контент страниц личного кабинета
                     */
                    do_action('woocommerce_account_content');
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
