<?php
/**
 * Footer Template
 * 
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;
?>

    <footer class="footer" id="contact">
        <div class="footer__inner">
            <div class="footer__cols footer__head">
                <h2><?php _e('Contact us', 'kerning-geoshop'); ?></h2>
                <p><?php _e('Have questions or need a quote?', 'kerning-geoshop'); ?> <br> <?php _e('Our team will provide technical guidance and help', 'kerning-geoshop'); ?> <br> <?php _e('you choose the right equipment or service.', 'kerning-geoshop'); ?></p>
            </div>
            <div class="footer__cols footer__middle">
                <div class="footer__info">
                    <div class="footer__contacts">
                        <a href="tel:+37123111390">+371 23 111 390</a>
                        <a href="mailto:diving.bloms@gmail.com">diving.bloms@gmail.com</a>
                    </div>
                    <div class="footer__pay">
                        <h4><?php _e('Payment options', 'kerning-geoshop'); ?></h4>
                        <div>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/visa.png" alt="visa">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/skrill.png" alt="skrill">
                        </div>
                    </div>
                </div>
                <div class="footer__form">
                    <?php echo do_shortcode('[contact-form-7 id="8050aa5" title="Contact form 1"]'); ?>
                </div>
            </div>
            <div class="footer__copyright">
                <p>Copyright © <?php echo date('Y'); ?> • <?php bloginfo('name'); ?></p>
            </div>
        </div>
    </footer>
</div><!-- .main-wrap -->

<?php if (function_exists('WC')) : ?>
<!-- Mini Cart Sidebar -->
<div class="mini-cart-sidebar">
    <div class="mini-cart-overlay"></div>
    <div class="mini-cart-panel">
        <div class="mini-cart-header">
            <span></span>
            <h3 class="mini-cart-title"><?php _e('Cart', 'kerning-geoshop'); ?></h3>
            <button class="mini-cart-close" aria-label="<?php _e('Close', 'kerning-geoshop'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="mini-cart-body">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (get_option('users_can_register')) : ?>
<!-- Account Sidebar -->
<div class="account-sidebar">
    <div class="account-overlay"></div>
    <div class="account-panel">
        <div class="account-header">
            <span></span>
            <h3 class="account-title"><?php echo is_user_logged_in() ? __('My Profile', 'kerning-geoshop') : __('Login / Sign up', 'kerning-geoshop'); ?></h3>
            <button class="account-close" aria-label="<?php _e('close', 'kerning-geoshop'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="account-body">
            <?php if (is_user_logged_in()) :
                $current_user = wp_get_current_user();
            ?>
                <div class="account-user-info">
                    <div class="user-avatar">
                        <?php echo get_avatar($current_user->ID, 80); ?>
                    </div>
                    <div class="user-details">
                        <h4 class="user-name"><?php echo esc_html($current_user->display_name); ?></h4>
                        <p class="user-email"><?php echo esc_html($current_user->user_email); ?></p>
                    </div>
                </div>
                <div class="account-menu">
                    <?php if (function_exists('wc_get_account_menu_items')) :
                        $menu_items = wc_get_account_menu_items();
                        foreach ($menu_items as $endpoint => $label) :
                            $url = wc_get_account_endpoint_url($endpoint);
                            $is_logout = ($endpoint === 'customer-logout');
                    ?>
                        <a href="<?php echo esc_url($url); ?>" class="account-menu-item <?php echo $is_logout ? 'logout' : ''; ?>">
                            <?php echo esc_html($label); ?>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
            <?php else : ?>
                <div class="account-tabs">
                    <button class="account-tab active" data-tab="login"><?php _e('Login', 'kerning-geoshop'); ?></button>
                    <button class="account-tab" data-tab="register"><?php _e('Sign up', 'kerning-geoshop'); ?></button>
                </div>
                <div class="account-forms">
                    <div class="account-form-wrapper active" id="login-form">
                        <form method="post" class="login-form" autocomplete="off">
                            <p class="form-row">
                                <input type="text" name="username" id="username" required placeholder="<?php _e('Login / Email*', 'kerning-geoshop'); ?>" autocomplete="off" />
                            </p>
                            <p class="form-row">
                                <input type="password" name="password" id="password" required placeholder="<?php _e('Password*', 'kerning-geoshop'); ?>" autocomplete="off" />
                            </p>
                            <p class="form-row form-row-remember">
                                <label>
                                    <input type="checkbox" name="rememberme" value="forever" /> <span><?php _e('Remember me', 'kerning-geoshop'); ?></span>
                                </label>
                            </p>
                            <p class="form-row">
                                <input type="hidden" name="security" value="<?php echo wp_create_nonce('ajax-login-nonce'); ?>" />
                                <button type="submit" name="login" class="button"><?php _e('Login', 'kerning-geoshop'); ?></button>
                            </p>
                            <p class="lost-password">
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php _e('Lost password?', 'kerning-geoshop'); ?></a>
                            </p>
                        </form>
                    </div>
                    <div class="account-form-wrapper" id="register-form">
                        <form method="post" class="register-form" autocomplete="off">
                            <p class="form-row">
                                <input type="text" name="username" id="reg_username" required placeholder="<?php _e('Login', 'kerning-geoshop'); ?>" autocomplete="off" />
                            </p>
                            <p class="form-row">
                                <input type="email" name="email" id="reg_email" required placeholder="<?php _e('Email', 'kerning-geoshop'); ?>" autocomplete="off" />
                            </p>
                            <p class="form-row">
                                <input type="password" name="password" id="reg_password" required placeholder="<?php _e('Password', 'kerning-geoshop'); ?>" autocomplete="off" />
                            </p>
                            <p class="form-row">
                                <input type="hidden" name="security" value="<?php echo wp_create_nonce('ajax-register-nonce'); ?>" />
                                <button type="submit" name="register" class="button"><?php _e('Sign up', 'kerning-geoshop'); ?></button>
                            </p>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Variation Modal -->
<div class="variation-modal">
    <div class="variation-modal-overlay"></div>
    <div class="variation-modal-panel">
        <div class="variation-modal-header">
            <span></span>
            <h3 class="variation-modal-title"><?php _e('Select Options', 'kerning-geoshop'); ?></h3>
            <button class="variation-modal-close" aria-label="<?php _e('Close', 'kerning-geoshop'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="variation-modal-body">
            <div class="variation-modal-product-info">
                <div class="variation-modal-image"></div>
                <div class="variation-modal-details">
                    <h4 class="variation-modal-product-title"></h4>
                    <div class="variation-modal-price"></div>
                </div>
            </div>
            <div class="variation-modal-options">
                <!-- Options will be loaded via AJAX -->
            </div>
        </div>
        <div class="variation-modal-actions">
            <button class="variation-add-to-cart button" disabled>
                <?php _e('Add to Cart', 'kerning-geoshop'); ?>
            </button>
            <button class="open-additional-products-from-variation">
                <?php _e('Additional Products', 'kerning-geoshop'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Additional Products Modal -->
<div class="additional-products-modal">
    <div class="additional-products-overlay"></div>
    <div class="additional-products-panel">
        <div class="additional-products-header">
            <span></span>
            <h3 class="additional-products-title"><?php _e('Additional Products', 'kerning-geoshop'); ?></h3>
            <button class="additional-products-close" aria-label="<?php _e('Close', 'kerning-geoshop'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="additional-products-body">
            <ul class="additional-products-list">
                <!-- Products will be loaded via AJAX -->
            </ul>
        </div>
    </div>
</div>

<?php wp_footer(); ?>

</body>
</html>