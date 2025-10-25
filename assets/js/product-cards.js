/**
 * Product Cards Functionality
 * Handles add to cart buttons on product cards in shop/archive pages
 */
(function($) {
    'use strict';

    console.log('=== PRODUCT CARDS JS LOADED ===');

    class ProductCards {
        constructor() {
            this.variationModal = $('.variation-modal');
            this.selectedAttributes = {};
            this.currentProductId = 0;
            this.variations = [];

            this.init();
        }

        init() {
            console.log('ProductCards initialized');

            // Handle simple product add to cart
            $(document).on('click', '.product .add-to-cart:not(.open-variation-modal)', this.handleSimpleProductClick.bind(this));

            // Handle variable product modal open
            $(document).on('click', '.product .add-to-cart.open-variation-modal', this.handleVariableProductClick.bind(this));

            // Close variation modal
            $('.variation-modal-close, .variation-modal-overlay').on('click', this.closeVariationModal.bind(this));

            // Variation option click
            $(document).on('click', '.variation-option', this.handleVariationOptionClick.bind(this));

            // Add to cart from variation modal
            $(document).on('click', '.variation-add-to-cart', this.addVariationToCart.bind(this));

            // Additional products button
            $(document).on('click', '.open-additional-products-from-variation', this.openAdditionalProducts.bind(this));

            // Close additional products modal
            $('.additional-products-close, .additional-products-overlay').on('click', this.closeAdditionalProducts.bind(this));

            // Add to cart from additional products modal
            $(document).on('click', '.additional-product-add-to-cart', this.addAdditionalProductToCart.bind(this));

            // Remove from cart in additional products modal
            // Use event delegation on document for dynamically created elements
            $(document).on('click', '.additional-product-remove', (e) => {
                console.log('Remove button clicked via delegation');
                this.removeAdditionalProductFromCart(e);
            });

            // Handle variation selection in additional products
            $(document).on('change', '.additional-variation-select', this.handleAdditionalVariationChange.bind(this));

            // ESC key to close modals
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (this.variationModal.hasClass('active')) {
                        this.closeVariationModal();
                    }
                    if ($('.additional-products-modal').hasClass('active')) {
                        this.closeAdditionalProducts();
                    }
                }
            });
        }

        /**
         * Handle click on simple product add to cart button
         */
        handleSimpleProductClick(e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(e.currentTarget);
            const productId = $button.data('product-id');

            console.log('Simple product clicked:', productId);

            if ($button.hasClass('loading')) {
                return;
            }

            $button.addClass('loading');

            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: productId,
                    quantity: 1
                },
                success: (response) => {
                    console.log('Add to cart response:', response);

                    if (response.success === false) {
                        const errorMsg = response.data && response.data.message ? response.data.message : 'Error adding to cart';
                        this.showNotification(errorMsg, 'error');
                    } else if (response.error) {
                        this.showNotification(response.error, 'error');
                    } else {
                        // Get product name
                        const productName = $button.closest('.product').find('.product__title h3').text();

                        // Update cart fragments
                        if (response.fragments) {
                            $.each(response.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Trigger WooCommerce events
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                        $(document.body).trigger('wc_fragments_refreshed');

                        // Show success notification
                        this.showNotification(`${productName} added to cart!`, 'success');

                        // Add visual feedback to button
                        $button.addClass('added');
                        setTimeout(() => {
                            $button.removeClass('added');
                        }, 2000);

                        // Open mini cart
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
                    $button.removeClass('loading');
                }
            });
        }

        /**
         * Handle click on variable product button - open variation modal
         */
        handleVariableProductClick(e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(e.currentTarget);
            const productId = $button.data('product-id');

            console.log('Variable product clicked:', productId);

            this.currentProductId = productId;
            this.selectedAttributes = {};

            // Load product variations
            this.loadProductVariations(productId);
        }

        /**
         * Load product variations via AJAX
         */
        loadProductVariations(productId) {
            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_product_variations',
                    product_id: productId
                },
                success: (response) => {
                    console.log('Variations response:', response);

                    if (response.success && response.data) {
                        this.renderVariationModal(response.data);
                        this.openVariationModal();
                    } else {
                        this.showNotification('Error loading product options', 'error');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error loading variations:', xhr, status, error);
                    this.showNotification('Error loading product options', 'error');
                }
            });
        }

        /**
         * Render variation modal with product data
         */
        renderVariationModal(data) {
            const { title, image, price, attributes, variations, has_additional_products } = data;

            // Store variations for later use
            this.variations = variations;

            console.log('Variations loaded:', variations);
            console.log('Attributes:', attributes);

            // Update modal content
            $('.variation-modal-product-title').text(title);
            $('.variation-modal-price').html(price);
            $('.variation-modal-image').html(`<img src="${image}" alt="${title}">`);

            // Render variation options
            const $optionsContainer = $('.variation-modal-options');
            $optionsContainer.empty();

            // Render each attribute (color, size, etc.)
            Object.keys(attributes).forEach((attrName) => {
                const options = attributes[attrName];

                if (!options || options.length === 0) return;

                const attrLabel = this.getAttributeLabel(attrName);

                const $attrDiv = $('<div>').addClass('variation-attribute').attr('data-attribute', attrName);
                const $label = $('<h4>').addClass('variation-attribute-label').text(attrLabel);
                const $optionsDiv = $('<div>').addClass('variation-attribute-options');

                // Render color swatches or regular buttons
                const isColorAttribute = attrName.includes('color');

                options.forEach((option) => {
                    const $option = $('<div>')
                        .addClass('variation-option')
                        .attr('data-value', option.slug)
                        .attr('data-attribute', attrName);

                    if (isColorAttribute && option.color) {
                        // Color swatch
                        $option
                            .css('background-color', option.color)
                            .css('width', '40px')
                            .css('height', '40px')
                            .css('border-radius', '50%')
                            .attr('title', option.name);

                        // Add border for white colors
                        if (option.color.toLowerCase() === '#ffffff' || option.color.toLowerCase() === '#fff') {
                            $option.css('border', '2px solid #ddd');
                        }
                    } else {
                        // Regular button with text
                        $option.text(option.name);
                    }

                    $optionsDiv.append($option);
                });

                $attrDiv.append($label, $optionsDiv);
                $optionsContainer.append($attrDiv);
            });

            // Show/hide additional products button
            if (has_additional_products) {
                $('.open-additional-products-from-variation').show();
            } else {
                $('.open-additional-products-from-variation').hide();
            }

            // Disable add to cart button until variation is selected
            $('.variation-add-to-cart').prop('disabled', true);
        }

        /**
         * Get human-readable attribute label
         */
        getAttributeLabel(attrName) {
            const labels = {
                'pa_color': 'Color',
                'pa_size': 'Size',
                'pa_material': 'Material'
            };

            return labels[attrName] || attrName.replace('pa_', '').replace(/_/g, ' ');
        }

        /**
         * Handle variation option click
         */
        handleVariationOptionClick(e) {
            const $option = $(e.currentTarget);
            const attrName = $option.data('attribute');
            const attrValue = $option.data('value');

            console.log('Variation option clicked:', attrName, attrValue);

            // Toggle selection in the same attribute group
            $option.siblings().removeClass('selected');
            $option.addClass('selected');

            // Update selected attributes
            this.selectedAttributes[attrName] = attrValue;

            console.log('Selected attributes:', this.selectedAttributes);

            // Find matching variation
            this.updateSelectedVariation();
        }

        /**
         * Update selected variation based on attributes
         */
        updateSelectedVariation() {
            // Check if all attributes are selected
            const requiredAttributes = $('.variation-attribute').map(function() {
                return $(this).data('attribute');
            }).get();

            const allSelected = requiredAttributes.every(attr => {
                return this.selectedAttributes.hasOwnProperty(attr) && this.selectedAttributes[attr];
            });

            console.log('All selected:', allSelected, 'Required:', requiredAttributes);
            console.log('Current selected attributes:', this.selectedAttributes);

            if (!allSelected) {
                console.log('Not all attributes selected yet');
                $('.variation-add-to-cart').prop('disabled', true);
                return;
            }

            console.log('All attributes selected, searching for matching variation...');

            // Find matching variation
            // Note: variation.attributes has keys like "attribute_pa_color"
            // but selectedAttributes has keys like "pa_color"
            const matchingVariation = this.variations.find(variation => {
                return requiredAttributes.every(attr => {
                    const variationAttrKey = 'attribute_' + attr;
                    return variation.attributes[variationAttrKey] === this.selectedAttributes[attr];
                });
            });

            console.log('Matching variation:', matchingVariation);
            console.log('Selected attributes:', this.selectedAttributes);
            console.log('Looking for variation with:', requiredAttributes.map(attr => 'attribute_' + attr));

            if (matchingVariation) {
                console.log('✓ Matching variation found!', matchingVariation);

                // Update price
                if (matchingVariation.price_html) {
                    $('.variation-modal-price').html(matchingVariation.price_html);
                }

                // Update image
                if (matchingVariation.image) {
                    $('.variation-modal-image img').attr('src', matchingVariation.image);
                }

                // Enable add to cart button
                $('.variation-add-to-cart').prop('disabled', false).data('variation-id', matchingVariation.variation_id);
                console.log('Button enabled with variation_id:', matchingVariation.variation_id);
            } else {
                console.warn('✗ No matching variation found!');
                console.log('Available variations:', this.variations);
                console.log('Looking for attributes:', this.selectedAttributes);
                $('.variation-add-to-cart').prop('disabled', true);
            }
        }

        /**
         * Add variation to cart
         */
        addVariationToCart() {
            const $button = $('.variation-add-to-cart');
            const variationId = $button.data('variation-id');

            if (!variationId || $button.prop('disabled')) {
                return;
            }

            console.log('Adding variation to cart:', variationId);

            const originalText = $button.text();
            $button.prop('disabled', true).addClass('loading').text('Adding...');

            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'woocommerce_ajax_add_to_cart',
                    product_id: this.currentProductId,
                    variation_id: variationId,
                    quantity: 1
                },
                success: (response) => {
                    console.log('Add variation to cart response:', response);

                    if (response.success === false) {
                        const errorMsg = response.data && response.data.message ? response.data.message : 'Error adding to cart';
                        this.showNotification(errorMsg, 'error');
                    } else if (response.error) {
                        this.showNotification(response.error, 'error');
                    } else {
                        // Update cart fragments
                        if (response.fragments) {
                            $.each(response.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Trigger WooCommerce events
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                        $(document.body).trigger('wc_fragments_refreshed');

                        // Show success notification
                        const productName = $('.variation-modal-product-title').text();
                        this.showNotification(`${productName} added to cart!`, 'success');

                        // Close modal
                        this.closeVariationModal();

                        // Open mini cart
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
                    $button.prop('disabled', false).removeClass('loading').text(originalText);
                }
            });
        }

        /**
         * Open variation modal
         */
        openVariationModal() {
            this.variationModal.addClass('active');
            $('body').addClass('variation-modal-open');

            // Stop lenis if available
            if (window.lenis) {
                window.lenis.stop();
            }
        }

        /**
         * Close variation modal
         */
        closeVariationModal() {
            this.variationModal.removeClass('active');
            $('body').removeClass('variation-modal-open');

            // Start lenis if available
            if (window.lenis) {
                window.lenis.start();
            }

            // Reset state
            this.selectedAttributes = {};
            this.currentProductId = 0;
            this.variations = [];
        }

        /**
         * Open additional products modal
         */
        openAdditionalProducts() {
            // Hide variation modal but keep it in background
            this.variationModal.addClass('hidden');

            // Load additional products
            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_additional_products',
                    product_id: this.currentProductId
                },
                success: (response) => {
                    if (response.success && response.data && response.data.products) {
                        this.renderAdditionalProducts(response.data.products);
                        $('.additional-products-modal').addClass('active');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error loading additional products:', xhr, status, error);
                }
            });
        }

        /**
         * Render additional products in modal
         */
        renderAdditionalProducts(products) {
            const $list = $('.additional-products-list');
            $list.empty();

            products.forEach(product => {
                const $item = $('<li>').addClass('additional-product-item').attr('data-product-id', product.id);

                // Mark if product is already in cart
                if (product.in_cart) {
                    $item.addClass('in-cart');
                }

                // Build variations HTML if variable product
                let variationsHTML = '';
                if (product.is_variable && product.variations && product.variations.length > 0) {
                    console.log(`Building variations HTML for product ${product.id}:`, product.variations);
                    variationsHTML = '<div class="additional-product-variations">';

                    // Group variations by attributes
                    const attributesMap = {};

                    product.variations.forEach((variation, idx) => {
                        console.log(`  Variation ${idx}:`, variation);
                        Object.keys(variation.attributes).forEach(attrName => {
                            console.log(`    Attribute: ${attrName}`, variation.attributes[attrName]);
                            if (!attributesMap[attrName]) {
                                attributesMap[attrName] = {
                                    label: variation.attributes[attrName].name,
                                    options: []
                                };
                            }

                            const attrValue = variation.attributes[attrName].value;
                            const attrSlug = variation.attributes[attrName].slug;

                            // Check if option already exists
                            const exists = attributesMap[attrName].options.some(opt => opt.slug === attrSlug);
                            if (!exists) {
                                attributesMap[attrName].options.push({
                                    value: attrValue,
                                    slug: attrSlug
                                });
                            }
                        });
                    });

                    console.log('Attributes map:', attributesMap);

                    // Render select dropdowns for each attribute
                    Object.keys(attributesMap).forEach(attrName => {
                        const attr = attributesMap[attrName];
                        console.log(`Creating select for attribute: ${attrName}, label: ${attr.label}`);
                        variationsHTML += `
                            <div class="variation-select-wrapper">
                                <label>${attr.label}:</label>
                                <select class="additional-variation-select variation-select"
                                        data-attribute="${attrName}"
                                        data-product-id="${product.id}">
                                    <option value="">Choose ${attr.label}</option>
                                    ${attr.options.map(opt => `<option value="${opt.slug}">${opt.value}</option>`).join('')}
                                </select>
                            </div>
                        `;
                    });

                    variationsHTML += '</div>';
                }

                const $content = $(`
                    <div class="additional-product-image">
                        <img src="${product.image}" alt="${product.title}">
                    </div>
                    <div class="additional-product-details">
                        <h5 class="additional-product-name">
                            <a href="${product.url}">${product.title}</a>
                        </h5>
                        <div class="additional-product-price">${product.price}</div>
                        ${variationsHTML}
                        <div class="additional-product-actions">
                            <button class="additional-product-add-to-cart ${product.in_cart ? 'added' : ''}"
                                    data-product-id="${product.id}"
                                    data-is-variable="${product.is_variable ? 'true' : 'false'}"
                                    ${product.is_variable || product.in_cart ? 'disabled' : ''}>
                                ${product.in_cart ? `
                                    <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 4px;">
                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                    </svg>
                                    Added
                                ` : 'Add to Cart'}
                            </button>
                            <button class="additional-product-remove"
                                    data-product-id="${product.id}"
                                    style="${product.in_cart ? 'display: flex;' : 'display: none;'}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>
                `);

                $item.html($content);
                $list.append($item);

                // Store variations data on the item
                if (product.is_variable && product.variations) {
                    $item.data('variations', product.variations);
                    console.log(`Product ${product.id} variations stored:`, product.variations);
                }
            });

            console.log('=== ALL ADDITIONAL PRODUCTS RENDERED ===');
        }

        /**
         * Close additional products modal
         */
        closeAdditionalProducts() {
            $('.additional-products-modal').removeClass('active');

            // Show variation modal again if it was open
            if (this.variationModal.hasClass('active')) {
                this.variationModal.removeClass('hidden');
            }
        }

        /**
         * Handle variation selection change in additional products
         */
        handleAdditionalVariationChange(e) {
            const $select = $(e.currentTarget);
            const productId = $select.data('product-id');
            const $item = $select.closest('.additional-product-item');
            const $button = $item.find('.additional-product-add-to-cart');

            console.log('=== VARIATION SELECTION CHANGED ===');
            console.log('Product ID:', productId);

            // Get all selects for this product
            const $selects = $item.find('.additional-variation-select');
            const selectedAttributes = {};
            let allSelected = true;

            $selects.each(function() {
                const value = $(this).val();
                const attr = $(this).data('attribute');

                console.log('Select:', attr, '= ', value);

                if (!value) {
                    allSelected = false;
                } else {
                    selectedAttributes[attr] = value;
                }
            });

            console.log('Selected attributes:', selectedAttributes);
            console.log('All selected:', allSelected);

            if (allSelected) {
                // Find matching variation
                const variations = $item.data('variations');
                console.log('Available variations:', variations);

                const matchingVariation = variations.find(variation => {
                    console.log('Checking variation:', variation);
                    const matches = Object.keys(selectedAttributes).every(attr => {
                        const variationAttrValue = variation.attributes[attr] ? variation.attributes[attr].slug : null;
                        const selectedValue = selectedAttributes[attr];
                        console.log(`  ${attr}: variation=${variationAttrValue}, selected=${selectedValue}, match=${variationAttrValue === selectedValue}`);
                        return variationAttrValue === selectedValue;
                    });
                    return matches;
                });

                console.log('Matching variation:', matchingVariation);

                if (matchingVariation) {
                    // Store variation ID on button
                    $button.data('variation-id', matchingVariation.variation_id);
                    $button.prop('disabled', false);

                    console.log('✓ Button enabled with variation_id:', matchingVariation.variation_id);
                    console.log('Button data after set:', $button.data());

                    // Update price if available
                    if (matchingVariation.price) {
                        $item.find('.additional-product-price').html(matchingVariation.price);
                    }
                } else {
                    console.warn('✗ No matching variation found!');
                    $button.prop('disabled', true);
                    $button.removeData('variation-id');
                }
            } else {
                console.log('Not all attributes selected yet');
                $button.prop('disabled', true);
                $button.removeData('variation-id');
            }
        }

        /**
         * Add additional product to cart
         */
        addAdditionalProductToCart(e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(e.currentTarget);
            const productId = $button.data('product-id');
            const isVariable = $button.data('is-variable') === 'true';
            const variationId = $button.data('variation-id') || 0;

            console.log('=== ADDITIONAL PRODUCT ADD TO CART ===');
            console.log('Product ID:', productId);
            console.log('Is Variable:', isVariable);
            console.log('Variation ID:', variationId);
            console.log('Button data:', $button.data());

            if ($button.hasClass('loading') || $button.prop('disabled')) {
                console.log('Button is loading or disabled, aborting');
                return;
            }

            // For variable products, make sure we have variation_id
            if (isVariable && !variationId) {
                console.error('Variable product but no variation_id!');
                this.showNotification('Please select product options', 'error');
                return;
            }

            // Disable button during request
            $button.prop('disabled', true);

            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            const ajaxData = {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: productId,
                quantity: 1
            };

            // Add variation ID if it's a variable product
            if (isVariable && variationId) {
                ajaxData.variation_id = variationId;
                console.log('Adding variation_id to request:', variationId);
            }

            console.log('AJAX Data:', ajaxData);

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: ajaxData,
                success: (response) => {
                    console.log('=== ADD TO CART RESPONSE ===');
                    console.log('Full response:', response);
                    console.log('response.success:', response.success);
                    console.log('response.error:', response.error);
                    console.log('response.data:', response.data);

                    if (response.success === false) {
                        const errorMsg = response.data && response.data.message ? response.data.message : 'Error adding to cart';
                        console.error('Add to cart failed:', errorMsg);
                        this.showNotification(errorMsg, 'error');
                        $button.prop('disabled', false);
                    } else if (response.error) {
                        console.error('Add to cart error:', response.error);
                        this.showNotification(response.error, 'error');
                        $button.prop('disabled', false);
                    } else {
                        console.log('✓ Add to cart successful');
                        // Update cart fragments
                        if (response.fragments) {
                            $.each(response.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Trigger ONLY wc_fragments_refreshed to update cart count
                        // DO NOT trigger 'added_to_cart' as it opens mini-cart in add.min.js
                        $(document.body).trigger('wc_fragments_refreshed');

                        // Update button state - change to "Added" with icon and show Remove button
                        $button.html(`
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 4px;">
                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                            </svg>
                            Added
                        `);
                        $button.addClass('added').prop('disabled', true);

                        // Show Remove button
                        $button.siblings('.additional-product-remove').css('display', 'flex');
                        $button.closest('.additional-product-item').addClass('in-cart');

                        // Get product name from the item
                        const productName = $button.closest('.additional-product-details').find('.additional-product-name a').text();

                        // Show success notification
                        this.showNotification(`${productName} added to cart!`, 'success');

                        // DO NOT open mini-cart for additional products
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Add to cart error:', xhr, status, error);
                    this.showNotification('Error adding product to cart', 'error');
                    $button.prop('disabled', false);
                }
            });
        }

        /**
         * Remove additional product from cart
         */
        removeAdditionalProductFromCart(e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(e.currentTarget);
            const productId = $button.data('product-id');

            console.log('=== REMOVE FROM CART ===');
            console.log('Product ID:', productId);
            console.log('Button:', $button);

            if ($button.hasClass('loading')) {
                console.log('Button is loading, aborting');
                return;
            }

            $button.addClass('loading');

            const ajaxUrl = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.ajax_url)
                ? wc_add_to_cart_params.ajax_url
                : '/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'remove_cart_item',
                    product_id: productId
                },
                success: (response) => {
                    console.log('Remove from cart response:', response);

                    if (response.success) {
                        // Update cart fragments
                        if (response.data && response.data.fragments) {
                            $.each(response.data.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Trigger cart update event
                        $(document.body).trigger('wc_fragments_refreshed');

                        // Update UI - hide Remove, show Add to Cart
                        $button.removeClass('loading').css('display', 'none');
                        const $addButton = $button.siblings('.additional-product-add-to-cart');
                        const isVariable = $addButton.data('is-variable') === 'true';
                        const $item = $button.closest('.additional-product-item');

                        console.log('Resetting add button state');
                        console.log('Is variable:', isVariable);

                        // Reset button to original state
                        $addButton.removeClass('added').html('Add to Cart');

                        // For variable products, check if variations are selected
                        if (isVariable) {
                            const $selects = $item.find('.additional-variation-select');
                            let allSelected = true;
                            $selects.each(function() {
                                if (!$(this).val()) {
                                    allSelected = false;
                                }
                            });
                            console.log('All variations selected:', allSelected);
                            $addButton.show().prop('disabled', !allSelected);
                        } else {
                            $addButton.show().prop('disabled', false);
                        }

                        $item.removeClass('in-cart');

                        // Get product name
                        const productName = $button.closest('.additional-product-details').find('.additional-product-name a').text();

                        // Show notification
                        this.showNotification(`${productName} removed from cart`, 'success');

                        console.log('✓ Remove completed successfully');
                    } else {
                        const errorMsg = response.data && response.data.message ? response.data.message : 'Error removing from cart';
                        this.showNotification(errorMsg, 'error');
                        $button.removeClass('loading');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Remove from cart error:', xhr, status, error);
                    this.showNotification('Error removing product from cart', 'error');
                    $button.removeClass('loading');
                }
            });
        }

        /**
         * Show notification
         */
        showNotification(message, type = 'success') {
            // Remove existing notifications
            $('.cart-notification').remove();

            const $notification = $(`
                <div class="cart-notification ${type === 'error' ? 'error' : 'success'}">
                    <span class="notification-icon">${type === 'error' ? '✕' : '✓'}</span>
                    <span class="notification-text">${message}</span>
                </div>
            `);

            $('body').append($notification);

            setTimeout(() => {
                $notification.addClass('show');
            }, 100);

            setTimeout(() => {
                $notification.removeClass('show');
                setTimeout(() => {
                    $notification.remove();
                }, 300);
            }, 3000);
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        console.log('Document ready - initializing ProductCards');
        new ProductCards();
    });

})(jQuery);
