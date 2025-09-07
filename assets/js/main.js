$(document).ready(function() {

    // =================================================================
    // 1. UI HELPERS (TOAST & CONFIRMATION)
    // =================================================================
    const toastContainer = $('#toast-container');
    const confirmationModal = $('#confirmation-modal');

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
    // 2. SITE-WIDE FUNCTIONALITY
    // =================================================================

    $('#mobile-menu-button').on('click', function() {
        $('#mobile-menu').slideToggle();
    });

    $('#profile-button').on('click', function() {
        $('#profile-dropdown').toggleClass('hidden');
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#profile-button, #profile-dropdown').length) {
            $('#profile-dropdown').addClass('hidden');
        }
    });

    function updateCartCount() {
        $.getJSON('api/cart/count.php', function(response) {
            if (response.success) {
                $('#cart-item-count').text(response.item_count);
            } else {
                $('#cart-item-count').text(0);
            }
        }).fail(function() {
            $('#cart-item-count').text(0);
        });
    }
    
    const backToTopButton = $('#back-to-top');
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            backToTopButton.fadeIn();
        } else {
            backToTopButton.fadeOut();
        }
    });
    backToTopButton.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 'slow');
    });

    // =================================================================
    // 3. CONTACT FORM AJAX SUBMISSION
    // =================================================================
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitButton = form.find('button[type="submit"]');
        const messageContainer = $('#contact-message-container');
        const originalButtonText = submitButton.html();

        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Sending...');
        messageContainer.slideUp().empty();

        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            subject: $('#subject').val(),
            message: $('#message').val()
        };

        $.ajax({
            url: 'api/contact.php', // The API endpoint
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    form[0].reset();
                    messageContainer.html(`<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">${response.message}</div>`).slideDown();
                } else {
                    messageContainer.html(`<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">${response.message}</div>`).slideDown();
                }
            },
            error: function() {
                 messageContainer.html(`<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">An unexpected error occurred. Please try again.</div>`).slideDown();
            },
            complete: function() {
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });


    // =================================================================
    // 4. HOMEPAGE-SPECIFIC FUNCTIONALITY
    // =================================================================
    if ($('.hero-slider').length) {
        new Swiper('.hero-slider', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }
    
   // --- Dynamic Collection Tabs ---
    $('.collection-tab').on('click', function(e) {
        e.preventDefault();
        
        // Update button styles
        $('.collection-tab').removeClass('bg-brand-dark text-white').addClass('bg-white text-brand-dark shadow-sm');
        $(this).removeClass('bg-white text-brand-dark shadow-sm').addClass('bg-brand-dark text-white');

        const category = $(this).data('category');
        const productGrid = $('#collection-grid');
        productGrid.html('<p class="col-span-full text-center text-gray-500 py-10">Loading designs...</p>');
        
        const filterData = {
            category: category,
            limit: 20 // Fetch up to 20 products for this section
        };

        // The "Featured" tab will show the latest products
        if (category === 'Featured') {
            filterData.sortBy = 'latest';
        }

        $.ajax({
            url: `api/shop/filter.php`,
            method: 'POST',
            // FIX: Send data as standard form data instead of JSON
            data: filterData, 
            dataType: 'json',
            success: function(response) {
                productGrid.empty();
                if (response.success && response.products.length > 0) {
                     response.products.forEach(product => {
                        const productCard = `
                            <div class="bg-white rounded-lg shadow-md overflow-hidden group transition-transform transform hover:-translate-y-1">
                                <a href="product-details.php?id=${product.id}" class="block">
                                    <div class="relative">
                                        <img src="${product.image_url}" alt="${product.name}" class="w-full h-56 object-cover">
                                    </div>
                                    <div class="p-4 text-center">
                                        <h3 class="text-md font-semibold text-brand-dark truncate">${product.name}</h3>
                                        <p class="text-brand-gray text-lg font-bold mt-1">&#8358;${parseFloat(product.price).toLocaleString()}</p>
                                    </div>
                                </a>
                            </div>
                        `;
                        productGrid.append(productCard);
                    });
                } else {
                    productGrid.html(`<p class="col-span-full text-center text-gray-500 py-10">No products found in the '${category}' collection yet.</p>`);
                }
            },
             error: function() {
                productGrid.html('<p class="col-span-full text-center text-red-500 py-10">Could not load products.</p>');
            }
        });
    });

    // Trigger click on the first tab on page load
    if ($('.collection-tab').length) {
        $('.collection-tab').first().trigger('click');
    }
    
    updateCartCount();
});

