/**
 * Single Product Page Functionality
 */
(function($) {
    'use strict';

    console.log('=== SINGLE PRODUCT JS LOADED ===');

    class SingleProduct {
        constructor() {
            console.log('SingleProduct constructor called');

            this.$form = $('.product-info form.cart');
            console.log('Form found:', this.$form.length, this.$form);

            this.$productId = this.$form.data('product-id');
            this.$productType = this.$form.data('product-type');
            this.selectedAttributes = {};

            console.log('Product ID:', this.$productId);
            console.log('Product Type:', this.$productType);

            this.init();
        }

        init() {
            console.log('init() called, form length:', this.$form.length);

            if (!this.$form.length) {
                console.warn('Form not found! Cannot initialize single product functionality');
                return;
            }

            // Initialize quantity buttons
            this.initQuantityButtons();

            // Load variations if variable product
            if (this.$productType === 'variable') {
                console.log('Product is variable, loading variations...');
                // Disable buttons initially until user selects variations
                this.$form.find('.single_add_to_cart_button, .buy-now-button').prop('disabled', true);
                this.loadVariations();
            } else {
                console.log('Product is not variable, type:', this.$productType);
            }

            // Add to cart button
            this.initAddToCart();

            // Buy now button
            this.initBuyNow();
        }

        /**
         * Quantity +/- buttons
         */
        initQuantityButtons() {
            const $qtyInput = this.$form.find('input.qty');
            const $minusBtn = this.$form.find('.qty-minus');
            const $plusBtn = this.$form.find('.qty-plus');

            $minusBtn.on('click', (e) => {
                e.preventDefault();
                let currentVal = parseInt($qtyInput.val()) || 1;
                let minVal = parseInt($qtyInput.attr('min')) || 1;

                if (currentVal > minVal) {
                    $qtyInput.val(currentVal - 1).trigger('change');
                }
            });

            $plusBtn.on('click', (e) => {
                e.preventDefault();
                let currentVal = parseInt($qtyInput.val()) || 1;
                let maxVal = parseInt($qtyInput.attr('max')) || Infinity;

                if (currentVal < maxVal) {
                    $qtyInput.val(currentVal + 1).trigger('change');
                }
            });
        }

        /**
         * Load product variations via AJAX
         */
        loadVariations() {
            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_product_variations',
                    product_id: this.$productId
                },
                success: (response) => {
                    if (response.success && response.data) {
                        this.renderVariations(response.data);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error loading variations:', error);
                }
            });
        }

        /**
         * Render color and size variations
         */
        renderVariations(data) {
            const { attributes, variations } = data;

            // Store required attributes for validation
            this.requiredAttributes = [];

            // Render colors (pa_color)
            if (attributes.pa_color && attributes.pa_color.length > 0) {
                this.renderColors(attributes.pa_color);
                this.$form.find('.product-info__colors').show();
                this.requiredAttributes.push('attribute_pa_color');
            } else {
                this.$form.find('.product-info__colors').hide();
            }

            // Render sizes (pa_size)
            if (attributes.pa_size && attributes.pa_size.length > 0) {
                this.renderSizes(attributes.pa_size);
                this.$form.find('.product-info__sizes').show();
                this.requiredAttributes.push('attribute_pa_size');
            } else {
                this.$form.find('.product-info__sizes').hide();
            }

            // Store variations for later use
            this.variations = variations;
        }

        /**
         * Render color swatches
         */
        renderColors(colors) {
            const $colorList = this.$form.find('.product-info__color-list');
            $colorList.empty();

            if (!colors || colors.length === 0) return;

            colors.forEach((color, index) => {
                const $colorDiv = $('<div>')
                    .addClass('product-info__color')
                    .attr('data-value', color.slug)
                    .css('background-color', color.color || '#cccccc');

                // Add border for white colors
                if (color.color && (color.color.toLowerCase() === '#ffffff' || color.color.toLowerCase() === '#fff')) {
                    $colorDiv.css('border', '1px solid #ddd');
                }

                // Don't select any color by default
                // User must choose

                $colorDiv.on('click', () => {
                    $colorList.find('.product-info__color').removeClass('active');
                    $colorDiv.addClass('active');
                    this.selectedAttributes.attribute_pa_color = color.slug;

                    // Update or create hidden input for color
                    let $colorInput = this.$form.find('input[name="attribute_pa_color"]');

                    if ($colorInput.length === 0) {
                        $colorInput = $('<input>').attr({
                            type: 'hidden',
                            name: 'attribute_pa_color'
                        });
                        this.$form.append($colorInput);
                    }
                    $colorInput.val(color.slug);

                    this.updateVariation();
                });

                $colorList.append($colorDiv);
            });
        }

        /**
         * Render size buttons with abbreviation support
         */
        renderSizes(sizes) {
            const $sizeList = this.$form.find('.product-info__size-list');
            $sizeList.empty();

            if (!sizes || sizes.length === 0) return;

            sizes.forEach((size, index) => {
                // Abbreviate long size names
                let displayName = size.name;
                if (displayName.toLowerCase() === 'made-to-measure' || displayName.toLowerCase() === 'made to measure') {
                    displayName = 'MTM';
                }

                const $sizeBtn = $('<button>')
                    .attr('type', 'button')
                    .addClass('product-info__size')
                    .attr('data-value', size.slug)
                    .attr('title', size.name) // Full name in tooltip
                    .text(displayName);

                // Don't select any size by default
                // User must choose

                $sizeBtn.on('click', (e) => {
                    e.preventDefault();

                    $sizeList.find('.product-info__size').removeClass('active');
                    $sizeBtn.addClass('active');
                    this.selectedAttributes.attribute_pa_size = size.slug;

                    // Update or create hidden input for size
                    let $sizeInput = this.$form.find('input[name="attribute_pa_size"]');

                    if ($sizeInput.length === 0) {
                        $sizeInput = $('<input>').attr({
                            type: 'hidden',
                            name: 'attribute_pa_size'
                        });
                        this.$form.append($sizeInput);
                    }
                    $sizeInput.val(size.slug);

                    this.updateVariation();
                });

                $sizeList.append($sizeBtn);
            });

            // Don't update variation - wait for user to select
        }

        /**
         * Update variation based on selected attributes
         */
        updateVariation() {
            if (!this.variations) return;

            // Check if all required attributes are selected
            const allAttributesSelected = this.requiredAttributes.every(attr => {
                return this.selectedAttributes.hasOwnProperty(attr) && this.selectedAttributes[attr];
            });

            // Try to find a matching variation even if not all attributes are selected
            // This allows us to update the image when only color is selected
            let matchingVariation = null;

            if (allAttributesSelected) {
                // All attributes selected - find exact match
                matchingVariation = this.variations.find(variation => {
                    return Object.keys(this.selectedAttributes).every(attr => {
                        return variation.attributes[attr] === this.selectedAttributes[attr];
                    });
                });
            } else {
                // Not all attributes selected - find partial match for image update
                matchingVariation = this.variations.find(variation => {
                    return Object.keys(this.selectedAttributes).every(attr => {
                        return variation.attributes[attr] === this.selectedAttributes[attr];
                    });
                });
            }

            if (matchingVariation) {
                // Update gallery image (even if not all attributes selected)
                if (matchingVariation.image) {
                    this.updateGalleryImage(matchingVariation.image);
                }

                // Only update other fields and enable buttons if all attributes are selected
                if (allAttributesSelected) {
                    // Update variation ID
                    console.log('Setting variation_id to:', matchingVariation.variation_id);
                    const $variationIdField = this.$form.find('.variation_id');
                    $variationIdField.val(matchingVariation.variation_id);

                    // Verify it was set
                    console.log('Verification - variation_id after setting:', $variationIdField.val());
                    console.log('Field element:', $variationIdField[0]);

                    // Update price if different
                    if (matchingVariation.price_html) {
                        this.$form.find('.product-info__price').html(matchingVariation.price_html);
                    }

                    // Update stock quantity
                    if (matchingVariation.max_qty) {
                        this.$form.find('input.qty').attr('max', matchingVariation.max_qty);
                    }

                    // Update SKU
                    if (matchingVariation.sku) {
                        this.$form.find('.product-info__sku-value').text(matchingVariation.sku);
                    }

                    // Enable add to cart button
                    this.$form.find('.single_add_to_cart_button, .buy-now-button').prop('disabled', false);
                } else {
                    // Not all attributes selected - keep buttons disabled
                    this.$form.find('.variation_id').val(0);
                    this.$form.find('.single_add_to_cart_button, .buy-now-button').prop('disabled', true);
                }
            } else {
                // No matching variation at all - disable button
                this.$form.find('.variation_id').val(0);
                this.$form.find('.single_add_to_cart_button, .buy-now-button').prop('disabled', true);
            }
        }

        /**
         * Update gallery main image
         */
        updateGalleryImage(imageUrl) {
            const $mainGallery = $('.product-gallery__main');

            if ($mainGallery.length && imageUrl) {
                // Update main image
                $mainGallery.find('img').attr('src', imageUrl);
                $mainGallery.find('a').attr('href', imageUrl);

                // If fancybox is initialized, update it
                if (typeof $.fancybox !== 'undefined') {
                    $mainGallery.find('a').attr('data-fancybox', 'gallery');
                }
            }
        }

        /**
         * Add to cart functionality
         */
        initAddToCart() {
            this.$form.on('submit', (e) => {
                e.preventDefault();

                const $button = this.$form.find('.single_add_to_cart_button');
                const originalText = $button.text();

                // Get form data
                const formData = new FormData(this.$form[0]);

                // For variable products, ensure variation_id is set
                if (this.$productType === 'variable') {
                    const variationId = this.$form.find('.variation_id').val();
                    if (!variationId || variationId === '0') {
                        // Build error message based on missing attributes
                        let missingAttrs = [];
                        if (this.requiredAttributes.includes('attribute_pa_color') && !this.selectedAttributes.attribute_pa_color) {
                            missingAttrs.push('color');
                        }
                        if (this.requiredAttributes.includes('attribute_pa_size') && !this.selectedAttributes.attribute_pa_size) {
                            missingAttrs.push('size');
                        }

                        const message = missingAttrs.length > 0
                            ? `Please select ${missingAttrs.join(' and ')} first`
                            : 'Please select all product options';

                        this.showNotification(message, 'error');
                        return;
                    }
                }

                $button.prop('disabled', true).text('Adding...');

                const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                    ? wc_add_to_cart_params.ajax_url
                    : '/wp-admin/admin-ajax.php';

                // DEBUG: Check variation_id before sending
                const variationIdField = this.$form.find('.variation_id');
                const variationIdByName = this.$form.find('input[name="variation_id"]');

                console.log('=== VARIATION ID DEBUG ===');
                console.log('Form element:', this.$form[0]);
                console.log('Variation ID field by class (.variation_id) - count:', variationIdField.length);
                console.log('Variation ID field by name [name="variation_id"] - count:', variationIdByName.length);
                console.log('Variation ID value (by class):', variationIdField.val());
                console.log('Variation ID value (by name):', variationIdByName.val());

                if (variationIdField.length > 1) {
                    console.warn('WARNING: Multiple variation_id fields found!');
                    variationIdField.each(function(i) {
                        console.log('Field ' + i + ':', this, 'value:', $(this).val());
                    });
                }

                console.log('Is variation_id inside form?', $.contains(this.$form[0], variationIdField[0]));
                console.log('All form inputs:', this.$form.find('input').map(function() {
                    return this.name + '=' + this.value;
                }).get());
                console.log('All inputs IN FORM (via this.$form.find):', this.$form.find('input[name]').length);
                console.log('All inputs names:', this.$form.find('input[name]').map(function() { return this.name; }).get());

                // Serialize ALL form data including hidden inputs
                let ajaxData = this.$form.serialize();

                // Add action parameter
                ajaxData += '&action=woocommerce_ajax_add_to_cart';

                console.log('Serialized data:', ajaxData);
                console.log('Data as object:', ajaxData.split('&').reduce((obj, pair) => {
                    const [key, value] = pair.split('=');
                    obj[decodeURIComponent(key)] = decodeURIComponent(value);
                    return obj;
                }, {}));
                console.log('========================');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: ajaxData,
                    success: (response) => {
                        // Check if response has success property (WordPress AJAX format)
                        if (response.success === false) {
                            const errorMsg = response.data && response.data.message ? response.data.message : 'Error adding to cart';
                            this.showNotification(errorMsg, 'error');
                        } else if (response.error) {
                            this.showNotification(response.error, 'error');
                        } else {
                            // Get product name and quantity
                            const productName = this.$form.find('.product-info__title').text();
                            const quantity = formData.get('quantity');

                            // Trigger WooCommerce cart update events
                            if (response.fragments) {
                                // Update cart fragments
                                $.each(response.fragments, function(key, value) {
                                    $(key).replaceWith(value);
                                });
                            }

                            // Trigger the standard WooCommerce added_to_cart event
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);

                            // Also trigger wc_fragments_refreshed for compatibility
                            $(document.body).trigger('wc_fragments_refreshed');

                            // Show success notification
                            this.showNotification(`Successfully added ${quantity} × ${productName} to cart!`, 'success');

                            // Open mini-cart automatically after adding to cart
                            setTimeout(() => {
                                $('.mini-cart-sidebar').addClass('active');
                                $('body').addClass('mini-cart-open');
                            }, 500);
                        }
                    },
                    error: (xhr, status, error) => {
                        console.error('Add to cart error:', xhr, status, error);
                        this.showNotification('Error adding product to cart', 'error');
                    },
                    complete: () => {
                        $button.prop('disabled', false).text(originalText);
                    }
                });
            });
        }

        /**
         * Buy now functionality
         */
        initBuyNow() {
            this.$form.find('.buy-now-button').on('click', (e) => {
                e.preventDefault();

                const $button = $(e.currentTarget);

                // Get form data
                const formData = new FormData(this.$form[0]);

                // For variable products, ensure variation_id is set
                if (this.$productType === 'variable') {
                    const variationId = this.$form.find('.variation_id').val();
                    if (!variationId || variationId === '0') {
                        // Build error message based on missing attributes
                        let missingAttrs = [];
                        if (this.requiredAttributes.includes('attribute_pa_color') && !this.selectedAttributes.attribute_pa_color) {
                            missingAttrs.push('color');
                        }
                        if (this.requiredAttributes.includes('attribute_pa_size') && !this.selectedAttributes.attribute_pa_size) {
                            missingAttrs.push('size');
                        }

                        const message = missingAttrs.length > 0
                            ? `Please select ${missingAttrs.join(' and ')} first`
                            : 'Please select all product options';

                        this.showNotification(message, 'error');
                        return;
                    }
                }

                $button.prop('disabled', true);

                const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                    ? wc_add_to_cart_params.ajax_url
                    : '/wp-admin/admin-ajax.php';

                const checkoutUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.checkout_url)
                    ? wc_add_to_cart_params.checkout_url
                    : '/checkout';

                // Serialize ALL form data including hidden inputs
                let buyNowData = this.$form.serialize();

                // Add action parameter
                buyNowData += '&action=woocommerce_ajax_add_to_cart';

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: buyNowData,
                    success: (response) => {
                        if (response.success === false) {
                            const errorMsg = response.data && response.data.message ? response.data.message : 'Error adding to cart';
                            this.showNotification(errorMsg, 'error');
                            $button.prop('disabled', false);
                        } else if (response.error) {
                            this.showNotification(response.error, 'error');
                            $button.prop('disabled', false);
                        } else {
                            // Update cart fragments before redirect
                            if (response.fragments) {
                                $.each(response.fragments, function(key, value) {
                                    $(key).replaceWith(value);
                                });
                            }

                            // Trigger cart update events
                            $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);

                            // Redirect to checkout after fragments update
                            window.location.href = checkoutUrl;
                        }
                    },
                    error: () => {
                        this.showNotification('Error processing request', 'error');
                        $button.prop('disabled', false);
                    }
                });
            });
        }

        /**
         * Show notification with auto-hide
         */
        showNotification(message, type = 'success') {
            // Remove existing notifications
            $('.product-notification').remove();

            // Create notification element
            const $notification = $('<div>')
                .addClass('product-notification')
                .addClass('notification-' + type)
                .html(`
                    <div class="notification-content">
                        <svg class="notification-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            ${type === 'success'
                                ? '<path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z" fill="currentColor"/>'
                                : '<path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM11 15H9V13H11V15ZM11 11H9V5H11V11Z" fill="currentColor"/>'
                            }
                        </svg>
                        <span class="notification-message">${message}</span>
                    </div>
                    <button class="notification-close" aria-label="Close">×</button>
                `)
                .appendTo('body');

            // Trigger animation
            setTimeout(() => {
                $notification.addClass('show');
            }, 10);

            // Close button handler
            $notification.find('.notification-close').on('click', () => {
                this.hideNotification($notification);
            });

            // Auto-hide after 4 seconds
            setTimeout(() => {
                this.hideNotification($notification);
            }, 4000);
        }

        /**
         * Hide notification with animation
         */
        hideNotification($notification) {
            $notification.removeClass('show');
            setTimeout(() => {
                $notification.remove();
            }, 300);
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        console.log('Document ready - initializing SingleProduct');
        console.log('jQuery version:', $.fn.jquery);
        new SingleProduct();
    });

})(jQuery);
