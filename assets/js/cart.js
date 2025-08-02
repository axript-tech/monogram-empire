$(document).ready(function() {

    /**
     * Helper function to display messages on the product details page.
     * @param {string} message - The message to display.
     * @param {boolean} isSuccess - True for a success message, false for an error.
     */
    function showCartMessage(message, isSuccess) {
        const messageContainer = $('#add-to-cart-message-container');
        const successClasses = 'bg-green-100 border-green-400 text-green-700';
        const errorClasses = 'bg-red-100 border-red-400 text-red-700';
        const messageHtml = `<div class="border px-4 py-3 rounded relative ${isSuccess ? successClasses : errorClasses}" role="alert">${message}</div>`;
        
        messageContainer.html(messageHtml).slideDown();
        setTimeout(() => messageContainer.slideUp(), 4000); // Hide after 4 seconds
    }

    /**
     * Checks if the current product is already in the cart and updates the button state.
     */
    function checkCartStatusForProductPage() {
        // This function should only run on the product details page
        if ($('#add-to-cart-form').length) {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            $.ajax({
                url: 'api/cart/get.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.cart_items) {
                        const isInCart = response.cart_items.some(item => item.product_id == productId);
                        if (isInCart) {
                            const addToCartButton = $('#add-to-cart-form button[type="submit"]');
                            addToCartButton.prop('disabled', true).html('<i class="fas fa-check-circle mr-2"></i> In Cart');
                            addToCartButton.removeClass('bg-brand-gold hover:bg-yellow-300').addClass('bg-gray-400 cursor-not-allowed');
                        }
                    }
                }
                // No error handling needed here, if it fails, the button just remains active.
            });
        }
    }

    /**
     * Handles adding a product to the cart from the product details page.
     */
    $('#add-to-cart-form').on('submit', function(e) {
        e.preventDefault();

        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        if (!productId) {
            showCartMessage('Could not identify the product. Please try again.', false);
            return;
        }

        const cartData = {
            product_id: productId,
        };

        const addToCartButton = $('#add-to-cart-form button[type="submit"]');
        addToCartButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Adding...');

        $.ajax({
            url: 'api/cart/add.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(cartData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showCartMessage(response.message, true);
                    $('#cart-item-count').text(response.total_items);
                    // Permanently change the button state on success
                    addToCartButton.html('<i class="fas fa-check-circle mr-2"></i> In Cart');
                    addToCartButton.removeClass('bg-brand-gold hover:bg-yellow-300').addClass('bg-gray-400 cursor-not-allowed');
                } else {
                    showCartMessage(response.message, false);
                    // Re-enable the button if another error occurred, but keep it disabled if already in cart
                    if(response.message.toLowerCase().includes('already in your cart')) {
                         addToCartButton.html('<i class="fas fa-check-circle mr-2"></i> In Cart');
                         addToCartButton.removeClass('bg-brand-gold hover:bg-yellow-300').addClass('bg-gray-400 cursor-not-allowed');
                    } else {
                        addToCartButton.prop('disabled', false).html('<i class="fas fa-shopping-cart mr-2"></i> Add to Cart');
                    }
                }
            },
            error: function(jqXHR) {
                // Re-enable the button on failure
                addToCartButton.prop('disabled', false).html('<i class="fas fa-shopping-cart mr-2"></i> Add to Cart');
                if (jqXHR.status === 401) {
                    showCartMessage('Please log in to add items to your cart.', false);
                    setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    showCartMessage('An unexpected error occurred. Please try again.', false);
                }
            }
        });
    });

    /**
     * Dynamically loads cart items on the cart.php page.
     */
    if (window.location.pathname.endsWith('cart.php')) {
        function loadCart() {
            $.ajax({
                url: 'api/cart/get.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const cartTableBody = $('#cart-items-table tbody');
                        const summaryContainer = $('#order-summary');
                        cartTableBody.empty();

                        if (response.cart_items.length === 0) {
                            $('#cart-container').html(`
                                <div class="text-center py-12">
                                    <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                                    <h2 class="text-2xl font-bold text-brand-dark mb-2">Your Cart is Empty</h2>
                                    <p class="text-gray-500 mb-6">Looks like you haven't added any designs yet.</p>
                                    <a href="shop.php" class="bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full hover:bg-yellow-300 transition-colors">
                                        Continue Shopping
                                    </a>
                                </div>
                            `);
                            return;
                        }

                        response.cart_items.forEach(item => {
                            const formattedPrice = parseFloat(item.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            const row = `
                                <tr class="border-b" data-item-id="${item.cart_item_id}">
                                    <td class="py-4 px-4 flex items-center">
                                        <img src="${item.image_url}" alt="${item.name}" class="w-20 h-20 object-cover rounded-md mr-4">
                                        <div>
                                            <p class="font-semibold text-brand-dark">${item.name}</p>
                                            <p class="text-sm text-gray-500">Digital Download</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-brand-gray">&#8358;${formattedPrice}</td>
                                    <td class="py-4 px-4 font-semibold text-brand-dark">&#8358;${formattedPrice}</td>
                                    <td class="py-4 px-4">
                                        <button class="remove-item-btn text-gray-400 hover:text-red-500" data-item-id="${item.cart_item_id}"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            `;
                            cartTableBody.append(row);
                        });

                        // Use a more reliable number formatting method
                        const formattedSubtotal = parseFloat(response.subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        summaryContainer.find('#summary-subtotal').text('₦' + formattedSubtotal);
                        summaryContainer.find('#summary-total').text('₦' + formattedSubtotal);

                    } else {
                        if (response.message.includes('logged in')) {
                             $('#cart-container').html(`
                                <div class="text-center py-12">
                                    <i class="fas fa-user-lock text-6xl text-gray-300 mb-4"></i>
                                    <h2 class="text-2xl font-bold text-brand-dark mb-2">Please Log In</h2>
                                    <p class="text-gray-500 mb-6">Log in to view your shopping cart.</p>
                                    <a href="login.php" class="bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full hover:bg-yellow-300 transition-colors">
                                        Go to Login
                                    </a>
                                </div>
                            `);
                        }
                    }
                },
                error: function() {
                    alert('Failed to load cart data.');
                }
            });
        }
        loadCart();

        /**
         * Handles removing an item from the cart.
         */
        $(document).on('click', '.remove-item-btn', function() {
            const itemId = $(this).data('item-id');
            
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            $.ajax({
                url: 'api/cart/remove.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ cart_item_id: itemId }),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadCart(); // Reloads the cart table
                        // Fetch and update the header cart count after removal
                        $.ajax({
                            url: 'api/cart/count.php',
                            method: 'GET',
                            dataType: 'json',
                            success: function(countResponse) {
                                if (countResponse.success) {
                                    $('#cart-item-count').text(countResponse.item_count);
                                }
                            }
                        });
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while removing the item.');
                }
            });
        });
    }
    
    // --- Initial Load ---
    checkCartStatusForProductPage();

});