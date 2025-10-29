<?php
/**
 * WooCommerce Template
 * САМЫЙ ВАЖНЫЙ ФАЙЛ - имеет наивысший приоритет для всех страниц WooCommerce
 */

if (!defined('ABSPATH')) exit;

get_header('shop');
?>

    <?php
        // ВАРИАНТ 1: Полный контроль - НЕ используем woocommerce_content()
        // Пишем свою структуру

        if (is_singular('product')) {
            // Страница товара
            while (have_posts()) {
                the_post();
                wc_get_template_part('content', 'single-product');
            }
        } elseif (is_checkout()) {
            // Страница Checkout - используется woocommerce/checkout/form-checkout.php
            while (have_posts()) {
                the_post();
                the_content(); // Вызывает шорткод [woocommerce_checkout], который загружает form-checkout.php
            }
        } elseif (is_cart()) {
            // Страница Корзины
            if (WC()->cart->is_empty()) {
                // Если корзина пуста
                wc_get_template('cart/cart-empty.php');
            } else {
                // Если есть товары - используем наш шаблон
                wc_get_template('cart/cart.php');
            }
        } else {
    // Категория / Магазин / Архив
    ?>

    <!-- ВАШ КАСТОМНЫЙ ЗАГОЛОВОК H1 -->
    <section class="catalog__head">
        <div class="catalog__head-title">
            <h1 class="page-title custom-title">
                <?php
                // Можете изменить заголовок как хотите
                if (is_product_category()) {
                    // Для категории
                    single_term_title();
                    // Или: echo "Категория: " . single_term_title('', false);
                } else {
                    // Для магазина
                    woocommerce_page_title();
                }
                ?>
            </h1>
        </div>

        <!-- Сортировка и количество товаров -->
        <div class="catalog__head-filter">
            <?php
                // Количество товаров
                woocommerce_result_count();

                // Сортировка
                woocommerce_catalog_ordering();
            ?>
        </div>
    </section>

    <section class="catalog">
        <div class="container">
            <?php
            if (woocommerce_product_loop()) :

                // Хуки перед списком товаров
                do_action('woocommerce_before_shop_loop');

                // Начало обертки товаров
                woocommerce_product_loop_start();

                // ЦИКЛ ТОВАРОВ
                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();
                        wc_get_template_part('content', 'product');
                    }
                }

                // Конец обертки товаров
                woocommerce_product_loop_end();

                // Хуки после списка товаров
                do_action('woocommerce_after_shop_loop');

            else :
                // Товары не найдены
                do_action('woocommerce_no_products_found');
            endif;
            ?>
        </div>
    </section>

    <?php
}
?>

<?php
get_footer('shop');