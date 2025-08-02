$(document).ready(function() {
    /**
     * Fetches the current cart item count from the server and updates the header.
     */
    function updateCartCount() {
        $.ajax({
            url: 'api/cart/count.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#cart-item-count').text(response.item_count);
                }
            }
        });
    }

    // --- Initial Page Load ---
    updateCartCount();

    // --- Swiper Hero Slider Initialization ---
    if ($('.hero-slider').length) {
        const swiper = new Swiper('.hero-slider', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }

    // --- Homepage Collection Tabs ---
    function loadCollection(category) {
        const grid = $('#collection-grid');
        grid.html('<p class="col-span-full text-center text-gray-500">Loading...</p>');

        const filters = {
            category: category === 'Featured' ? 'All' : category,
            sortBy: 'latest',
            limit: 4 // Limit to 4 products for the homepage
        };

        $.ajax({
            url: 'api/shop/filter.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(filters),
            dataType: 'json',
            success: function(response) {
                grid.empty();
                if (response.success && response.products.length > 0) {
                    response.products.forEach(product => {
                        const productCard = `
                            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                                <div class="relative">
                                    <img src="./${product.image_url}" alt="${product.name}" class="w-full h-64 object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="product-details.php?id=${product.id}" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                                    </div>
                                </div>
                                <div class="p-4 text-center">
                                    <h3 class="text-lg font-semibold text-brand-dark">${product.name}</h3>
                                    <p class="text-brand-gray">&#8358;${parseFloat(product.price).toLocaleString()}</p>
                                </div>
                            </div>
                        `;
                        grid.append(productCard);
                    });
                } else {
                    grid.html('<p class="col-span-full text-center text-gray-500">No products found in this category.</p>');
                }
            },
            error: () => grid.html('<p class="col-span-full text-center text-red-500">Failed to load products.</p>')
        });
    }

    $('.collection-tab').on('click', function() {
        const category = $(this).data('category');
        $('.collection-tab').removeClass('bg-brand-dark text-white').addClass('bg-white text-brand-dark');
        $(this).removeClass('bg-white text-brand-dark').addClass('bg-brand-dark text-white');
        loadCollection(category);
    });

    // Initial load for the "Featured" tab
    if ($('#collection-grid').length) {
        loadCollection('Featured');
    }


    // Mobile menu toggle
    $('#mobile-menu-button').on('click', function() {
        $('#mobile-menu').slideToggle();
    });

    // Profile Dropdown Toggle
    $('#profile-button').on('click', function(e) {
        e.stopPropagation();
        $('#profile-dropdown').toggleClass('hidden');
    });

    $(document).on('click', function() {
        $('#profile-dropdown').addClass('hidden');
    });

    // Set active navigation link based on current page
    const currentPage = window.location.pathname.split("/").pop();
    if (currentPage) {
        $('.nav-link').removeClass('active');
        $(`.nav-link[href="${currentPage}"]`).addClass('active');
    } else {
        $('.nav-link[href="index.php"]').addClass('active');
    }

    // FAQ Accordion
    $('#faq-accordion .faq-question').on('click', function() {
        const currentAnswer = $(this).next('.faq-answer');
        const currentIcon = $(this).find('i');

        if (currentAnswer.is(':visible')) {
            currentAnswer.slideUp();
            currentIcon.removeClass('rotate-180');
        } else {
            $('.faq-answer').slideUp();
            $('.faq-question i').removeClass('rotate-180');
            currentAnswer.slideDown();
            currentIcon.addClass('rotate-180');
        }
    });

    // --- Product Details Page Logic (Display only) ---
    $('.thumbnail-image').on('click', function() {
        const newImageSrc = $(this).attr('src');
        $('#main-product-image').attr('src', newImageSrc);
        $('.thumbnail-image').removeClass('border-brand-gold').addClass('border-transparent');
        $(this).removeClass('border-transparent').addClass('border-brand-gold');
    });

    $('#details-accordion .details-question').on('click', function() {
        const answer = $(this).next('.details-answer');
        const icon = $(this).find('i');
        answer.slideToggle();
        icon.toggleClass('rotate-180');
    });


    // --- Contact Form Logic ---
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        const messageContainer = $('#contact-message-container');
        messageContainer.slideUp().empty();

        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            subject: $('#subject').val(),
            message: $('#message').val()
        };

        $.ajax({
            url: 'api/contact.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                const successClasses = 'bg-green-100 border-green-400 text-green-700';
                const errorClasses = 'bg-red-100 border-red-400 text-red-700';
                const messageHtml = `<div class="border px-4 py-3 rounded relative ${response.success ? successClasses : errorClasses}" role="alert">${response.message}</div>`;
                
                messageContainer.html(messageHtml).slideDown();

                if (response.success) {
                    $('#contact-form')[0].reset();
                }
            },
            error: function() {
                const errorHtml = `<div class="border px-4 py-3 rounded relative bg-red-100 border-red-400 text-red-700" role="alert">An unexpected error occurred. Please try again.</div>`;
                messageContainer.html(errorHtml).slideDown();
            }
        });
    });

});
