<?php
/**
 * My Account Dashboard
 *
 * @package CustomShop
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$customer = new WC_Customer( get_current_user_id() );

// Get customer stats
$customer_orders = wc_get_orders( array(
    'customer' => get_current_user_id(),
    'limit'    => -1,
) );

$total_orders = count( $customer_orders );
$completed_orders = count( wc_get_orders( array(
    'customer' => get_current_user_id(),
    'status'   => 'completed',
    'limit'    => -1,
) ) );

$total_spent = 0;
foreach ( $customer_orders as $order ) {
    if ( $order->get_status() === 'completed' ) {
        $total_spent += $order->get_total();
    }
}

// Get recent orders
$recent_orders = wc_get_orders( array(
    'customer' => get_current_user_id(),
    'limit'    => 3,
    'orderby'  => 'date',
    'order'    => 'DESC',
) );
?>

<div class="dashboard-welcome">
    <div class="dashboard-welcome__content">
        <h2><?php printf( __( 'Hello, %s!', 'kerning-geoshop' ), '<strong>' . esc_html( $current_user->display_name ) . '</strong>' ); ?></h2>
        <p><?php _e( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.', 'kerning-geoshop' ); ?></p>
    </div>
    <div class="dashboard-welcome__avatar">
        <?php echo get_avatar( $current_user->ID, 120 ); ?>
    </div>
</div>

<div class="dashboard-stats">
    <div class="dashboard-stat-card">
        <div class="dashboard-stat-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 21V5C16 4.44772 15.5523 4 15 4H5C4.44772 4 4 4.44772 4 5V21M16 21H20M16 21H8M20 21C20.5523 21 21 20.5523 21 20V9C21 8.44772 20.5523 8 20 8H16M8 21H4M4 21C3.44772 21 3 20.5523 3 20V5" stroke="#176DAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 8H12M8 12H12M8 16H12" stroke="#176DAA" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="dashboard-stat-card__content">
            <div class="dashboard-stat-card__value"><?php echo esc_html( $total_orders ); ?></div>
            <div class="dashboard-stat-card__label"><?php _e( 'Total Orders', 'kerning-geoshop' ); ?></div>
        </div>
    </div>

    <div class="dashboard-stat-card">
        <div class="dashboard-stat-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="dashboard-stat-card__content">
            <div class="dashboard-stat-card__value"><?php echo esc_html( $completed_orders ); ?></div>
            <div class="dashboard-stat-card__label"><?php _e( 'Completed', 'kerning-geoshop' ); ?></div>
        </div>
    </div>

    <div class="dashboard-stat-card">
        <div class="dashboard-stat-card__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 6V18M12 6L7 11M12 6L17 11" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="dashboard-stat-card__content">
            <div class="dashboard-stat-card__value"><?php echo wc_price( $total_spent ); ?></div>
            <div class="dashboard-stat-card__label"><?php _e( 'Total Spent', 'kerning-geoshop' ); ?></div>
        </div>
    </div>
</div>

<?php if ( ! empty( $recent_orders ) ) : ?>
<div class="dashboard-recent-orders">
    <h3><?php _e( 'Recent Orders', 'kerning-geoshop' ); ?></h3>
    <div class="dashboard-orders-list">
        <?php foreach ( $recent_orders as $order ) : ?>
            <div class="dashboard-order-card">
                <div class="dashboard-order-card__header">
                    <div class="dashboard-order-card__number">
                        <span class="label"><?php _e( 'Order', 'kerning-geoshop' ); ?></span>
                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
                            #<?php echo $order->get_order_number(); ?>
                        </a>
                    </div>
                    <div class="dashboard-order-card__date">
                        <?php echo wc_format_datetime( $order->get_date_created() ); ?>
                    </div>
                </div>
                <div class="dashboard-order-card__body">
                    <div class="dashboard-order-card__items">
                        <?php
                        $items = $order->get_items();
                        $item_count = count( $items );
                        ?>
                        <span><?php printf( _n( '%s item', '%s items', $item_count, 'kerning-geoshop' ), $item_count ); ?></span>
                    </div>
                    <div class="dashboard-order-card__total">
                        <?php echo $order->get_formatted_order_total(); ?>
                    </div>
                    <div class="dashboard-order-card__status">
                        <span class="status-badge status-<?php echo esc_attr( $order->get_status() ); ?>">
                            <?php echo wc_get_order_status_name( $order->get_status() ); ?>
                        </span>
                    </div>
                </div>
                <div class="dashboard-order-card__footer">
                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="button-link">
                        <?php _e( 'View Order', 'kerning-geoshop' ); ?>
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="dashboard-view-all">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="button button-secondary">
            <?php _e( 'View All Orders', 'kerning-geoshop' ); ?>
        </a>
    </div>
</div>
<?php else : ?>
<div class="dashboard-no-orders">
    <div class="dashboard-no-orders__icon">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3 3H5L5.4 5M5.4 5H21L17 13H7M5.4 5L7 13M7 13L5.707 14.293C5.077 14.923 5.523 16 6.414 16H17M17 16C15.8954 16 15 16.8954 15 18C15 19.1046 15.8954 20 17 20C18.1046 20 19 19.1046 19 18C19 16.8954 18.1046 16 17 16ZM9 18C9 19.1046 8.10457 20 7 20C5.89543 20 5 19.1046 5 18C5 16.8954 5.89543 16 7 16C8.10457 16 9 16.8954 9 18Z" stroke="#999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <h3><?php _e( 'No orders yet', 'kerning-geoshop' ); ?></h3>
    <p><?php _e( 'Start shopping and your orders will appear here', 'kerning-geoshop' ); ?></p>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="button">
        <?php _e( 'Start Shopping', 'kerning-geoshop' ); ?>
    </a>
</div>
<?php endif; ?>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );
?>
