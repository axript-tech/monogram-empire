$(document).ready(function() {

    /**
     * Handles adding a product to the cart from the product details page.
     */
    // Note: This selector needs to be specific to the button on the product-details.php page
    $('#details-accordion').closest('form').on('submit', function(e) {
        e.preventDefault();

        // In a real app, you'd get the product ID from a data attribute on the form or button.
        // For this example, we'll parse it from the URL.
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        if (!productId) {
            alert('Could not identify the product. Please go back to the shop and try again.');
            return;
        }

        const cartData = {
            product_id: productId,
        };

        $.ajax({
            url: 'api/cart/add.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(cartData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Update the cart count in the header
                    $('#cart-item-count').text(response.total_items);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(jqXHR) {
                if (jqXHR.status === 401) {
                    alert('Please log in to add items to your cart.');
                    window.location.href = 'login.php';
                } else {
                    alert('An unexpected error occurred. Please try again.');
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
                        cartTableBody.empty(); // Clear existing rows

                        if (response.cart_items.length === 0) {
                            // Show empty cart message
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
                            const row = `
                                <tr class="border-b" data-item-id="${item.cart_item_id}">
                                    <td class="py-4 px-4 flex items-center">
                                        <img src="${item.image_url}" alt="${item.name}" class="w-20 h-20 object-cover rounded-md mr-4">
                                        <div>
                                            <p class="font-semibold text-brand-dark">${item.name}</p>
                                            <p class="text-sm text-gray-500">Digital Download</p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-brand-gray">&#8358;${parseFloat(item.price).toLocaleString()}</td>
                                    <td class="py-4 px-4 font-semibold text-brand-dark">&#8358;${parseFloat(item.price).toLocaleString()}</td>
                                    <td class="py-4 px-4">
                                        <button class="remove-item-btn text-gray-400 hover:text-red-500" data-item-id="${item.cart_item_id}"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            `;
                            cartTableBody.append(row);
                        });

                        // Update summary
                        summaryContainer.find('#summary-subtotal').text('₦' + response.subtotal.toLocaleString());
                        summaryContainer.find('#summary-total').text('₦' + response.subtotal.toLocaleString());

                    } else {
                         // Handle case where user is not logged in
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
                        // Reload the cart to show the changes
                        loadCart();
                        // You might also want to update the header cart count here
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

});
