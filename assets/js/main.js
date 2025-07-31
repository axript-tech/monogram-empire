$(document).ready(function() {
    // Mobile menu toggle
    $('#mobile-menu-button').on('click', function() {
        $('#mobile-menu').slideToggle();
    });

    // Set active navigation link based on current page
    const currentPage = window.location.pathname.split("/").pop();
    if (currentPage) {
        $('.nav-link').removeClass('active');
        $(`.nav-link[href="${currentPage}"]`).addClass('active');
    } else {
        // Default to home if on root
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

    // --- Product Details Page Logic ---

    // Image Gallery
    $('.thumbnail-image').on('click', function() {
        // Get the src from the clicked thumbnail
        const newImageSrc = $(this).attr('src').replace('150x150', '600x600');
        
        // Update the main image src
        $('#main-product-image').attr('src', newImageSrc);
        
        // Update active border style
        $('.thumbnail-image').removeClass('border-brand-gold').addClass('border-transparent');
        $(this).removeClass('border-transparent').addClass('border-brand-gold');
    });

    // Quantity Selector
    $('.quantity-btn').on('click', function() {
        const action = $(this).data('action');
        const input = $('#quantity-input');
        let currentValue = parseInt(input.val());

        if (action === 'increment') {
            currentValue++;
        } else if (action === 'decrement' && currentValue > 1) {
            currentValue--;
        }
        
        input.val(currentValue);
    });

    // Details Accordion
    $('#details-accordion .details-question').on('click', function() {
        const answer = $(this).next('.details-answer');
        const icon = $(this).find('i');
        answer.slideToggle();
        icon.toggleClass('rotate-180');
    });


    // TODO: Add functions to update cart item count dynamically via an API call
    // function updateCartCount() {
    //     $.ajax({
    //         url: 'api/cart/get.php',
    //         method: 'GET',
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.success) {
    //                 let totalItems = 0;
    //                 response.cart_items.forEach(item => {
    //                     totalItems += parseInt(item.quantity);
    //                 });
    //                 $('#cart-item-count').text(totalItems);
    //             }
    //         }
    //     });
    // }
    // updateCartCount(); 
});
