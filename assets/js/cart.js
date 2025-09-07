$(document).ready(function() {

    // =================================================================
    // 1. UI HELPERS (Confirmation & Toasts)
    // =================================================================
    const toastContainer = $('#toast-container');
    const confirmationModal = $('#confirmation-modal');

    /**
     * Shows a toast notification.
     * @param {string} message - The message to display.
     * @param {boolean} isSuccess - True for success, false for error.
     */
    function showToast(message, isSuccess = true) {
        const icon = isSuccess ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
        const bgColor = isSuccess ? 'bg-green-500' : 'bg-red-500';
        const toast = $(`<div class="opacity-0 transform translate-y-2 ${bgColor} text-white p-4 rounded-lg shadow-lg flex items-center mb-2 transition-all duration-300"><div class="mr-3">${icon}</div><div>${message}</div></div>`);
        
        toastContainer.append(toast);
        setTimeout(() => {
            toast.removeClass('opacity-0 translate-y-2');
        }, 10); 

        setTimeout(() => {
            toast.addClass('opacity-0');
            toast.on('transitionend', () => toast.remove());
        }, 4000);
    }

    /**
     * Shows a confirmation modal.
     * @param {object} options - Configuration for the modal.
     */
    function showConfirmation({ title, message, confirmText, onConfirm }) {
        confirmationModal.find('#confirmation-title').text(title);
        confirmationModal.find('#confirmation-message').text(message);
        const confirmBtn = confirmationModal.find('#confirm-action-btn');
        confirmBtn.text(confirmText);
        
        confirmBtn.off('click').on('click', () => { 
            onConfirm(); 
            confirmationModal.addClass('hidden'); 
        });
        
        confirmationModal.find('#confirm-cancel-btn').off('click').on('click', () => confirmationModal.addClass('hidden'));
        
        confirmationModal.removeClass('hidden');
    }


    // =================================================================
    // 2. PRODUCT DETAILS PAGE SPECIFIC LOGIC
    // =================================================================

    /**
     * Handles thumbnail clicks to change the main product image.
     */
    $('.thumbnail-image').on('click', function() {
        const newImageSrc = $(this).attr('src');
        $('#main-product-image').attr('src', newImageSrc);
        $('.thumbnail-image').removeClass('border-brand-gold').addClass('border-transparent');
        $(this).removeClass('border-transparent').addClass('border-brand-gold');
    });

    $('.thumbnail-image').first().addClass('border-brand-gold').removeClass('border-transparent');


    /**
     * Helper function to display messages on the product details page.
     */
    function showCartMessage(message, isSuccess) {
        const messageContainer = $('#add-to-cart-message-container');
        const successClasses = 'bg-green-100 border-green-400 text-green-700';
        const errorClasses = 'bg-red-100 border-red-400 text-red-700';
        const messageHtml = `<div class="border px-4 py-3 rounded relative ${isSuccess ? successClasses : errorClasses}" role="alert">${message}</div>`;
        
        messageContainer.html(messageHtml).slideDown();
        setTimeout(() => messageContainer.slideUp(), 4000);
    }

    /**
     * Checks if the current product is already in the cart and updates the button state.
     */
    function checkCartStatusForProductPage() {
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
                            addToCartButton.removeClass('bg-brand-dark hover:bg-gray-700').addClass('bg-gray-400 cursor-not-allowed');
                        }
                    }
                }
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
                    addToCartButton.html('<i class="fas fa-check-circle mr-2"></i> In Cart');
                    addToCartButton.removeClass('bg-brand-dark hover:bg-gray-700').addClass('bg-gray-400 cursor-not-allowed');
                } else {
                    showCartMessage(response.message, false);
                    if(response.message.toLowerCase().includes('already in your cart')) {
                         addToCartButton.html('<i class="fas fa-check-circle mr-2"></i> In Cart');
                         addToCartButton.removeClass('bg-brand-dark hover:bg-gray-700').addClass('bg-gray-400 cursor-not-allowed');
                    } else {
                        addToCartButton.prop('disabled', false).html('<i class="fas fa-shopping-cart mr-2"></i> Add to Cart');
                    }
                }
            },
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                if (response && response.message) {
                    showCartMessage(response.message, false);
                    if (response.message.toLowerCase().includes('already in your cart')) {
                        addToCartButton.html('<i class="fas fa-check-circle mr-2"></i> In Cart');
                        addToCartButton.removeClass('bg-brand-dark hover:bg-gray-700').addClass('bg-gray-400 cursor-not-allowed');
                    } else {
                         addToCartButton.prop('disabled', false).html('<i class="fas fa-shopping-cart mr-2"></i> Add to Cart');
                    }
                } else {
                    showCartMessage('An unexpected error occurred. Please try again.', false);
                    addToCartButton.prop('disabled', false).html('<i class="fas fa-shopping-cart mr-2"></i> Add to Cart');
                }
            }
        });
    });

    // =================================================================
    // 3. CART PAGE SPECIFIC LOGIC
    // =================================================================

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

        $(document).on('click', '.remove-item-btn', function() {
            const itemId = $(this).data('item-id');
            
            showConfirmation({
                title: 'Remove Item',
                message: 'Are you sure you want to remove this item from your cart?',
                confirmText: 'Remove',
                onConfirm: function() {
                    $.ajax({
                        url: 'api/cart/remove.php',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ cart_item_id: itemId }),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToast('Item removed successfully.', true);
                                loadCart(); // Reloads the cart table
                                // Fetch and update the header cart count
                                $.get('api/cart/count.php', (countResponse) => {
                                    if(countResponse.success) $('#cart-item-count').text(countResponse.item_count);
                                });
                            } else {
                                showToast(response.message, false);
                            }
                        },
                        error: function() {
                            showToast('An error occurred while removing the item.', false);
                        }
                    });
                }
            });
        });
    }
    
    // =================================================================
    // 4. INITIAL LOAD
    // =================================================================
    checkCartStatusForProductPage();

});

