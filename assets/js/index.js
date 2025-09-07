$(document).ready(function() {

    // --- Hero Slider ---
    if ($('.hero-slider').length) {
        new Swiper('.hero-slider', {
            loop: true,
            effect: 'fade',
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
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
});

