/**
 * AJAX Shop Filtering and Pagination
 *
 * @package CustomShop
 */

jQuery(function($) {
    'use strict';

    // Get admin-ajax URL
    var ajaxUrl = customshop_ajax_filters.ajax_url;

    // AJAX сортировка
    $(document).on('change', '.woocommerce-ordering select.orderby', function(e) {
        e.preventDefault();

        var orderby = $(this).val();
        var $container = $('.catalog .container');

        // Показываем loader
        $container.css('opacity', '0.5');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'filter_products',
                orderby: orderby,
                paged: 1
            },
            success: function(response) {
                if (response.success) {
                    $container.html(response.data.html);
                    $container.css('opacity', '1');

                    // Обновляем URL
                    var url = new URL(window.location);
                    url.searchParams.set('orderby', orderby);
                    url.searchParams.delete('paged');
                    window.history.pushState({}, '', url);

                    // Скролл наверх
                    $('html, body').animate({
                        scrollTop: $('.catalog__head').offset().top - 100
                    }, 500);
                }
            },
            error: function() {
                $container.css('opacity', '1');
            }
        });
    });

    // AJAX пагинация
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();

        var href = $(this).attr('href');
        var page = 1;

        // Пытаемся извлечь номер страницы из URL
        var match = href.match(/paged=(\d+)/);
        if (match) {
            page = match[1];
        } else {
            match = href.match(/page\/(\d+)/);
            if (match) {
                page = match[1];
            }
        }

        var orderby = $('.woocommerce-ordering select.orderby').val();
        var $container = $('.catalog .container');

        // Показываем loader
        $container.css('opacity', '0.5');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'filter_products',
                orderby: orderby,
                paged: page
            },
            success: function(response) {
                if (response.success) {
                    $container.html(response.data.html);
                    $container.css('opacity', '1');

                    // Обновляем URL
                    var url = new URL(window.location);
                    if (page > 1) {
                        url.searchParams.set('paged', page);
                    } else {
                        url.searchParams.delete('paged');
                    }
                    window.history.pushState({}, '', url);

                    // Скролл наверх
                    $('html, body').animate({
                        scrollTop: $('.catalog__head').offset().top - 100
                    }, 500);
                }
            },
            error: function() {
                $container.css('opacity', '1');
            }
        });
    });
});
