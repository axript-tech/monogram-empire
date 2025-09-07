$(document).ready(function() {

    // --- State Management ---
    let currentFilters = {
        searchTerm: '',
        category: 'All',
        maxPrice: 50000,
        sortBy: 'default',
        page: 1
    };
    let debounceTimer;

    // --- UI Elements ---
    const filterSidebar = $('#filter-sidebar');
    const filterBackdrop = $('#filter-backdrop');

    /**
     * Renders product cards into the grid.
     */
    function renderProducts(products) {
        const grid = $('#products-grid');
        grid.empty();

        if (products.length === 0) {
            grid.html('<p class="col-span-full text-center text-gray-500">No products match your criteria.</p>');
            return;
        }

        products.forEach(product => {
            const productCard = `
                <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                    <div class="relative">
                        <img src="${product.image_url}" alt="${product.name}" class="w-full h-64 object-cover">
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
    }

    /**
     * Renders pagination controls.
     */
    function renderPagination(pagination) {
        const container = $('#pagination-container');
        container.empty();
        if (pagination.total_pages <= 1) return;

        let paginationHtml = '<nav class="flex items-center space-x-2">';
        if (pagination.page > 1) {
            paginationHtml += `<a href="#" class="page-link px-4 py-2 text-gray-500 hover:text-brand-dark" data-page="${pagination.page - 1}">&laquo;</a>`;
        }

        for (let i = 1; i <= pagination.total_pages; i++) {
            const activeClass = i === pagination.page ? 'bg-brand-dark text-white' : 'bg-white text-gray-700 hover:bg-gray-200';
            paginationHtml += `<a href="#" class="page-link px-4 py-2 rounded-md ${activeClass}" data-page="${i}">${i}</a>`;
        }

        if (pagination.page < pagination.total_pages) {
            paginationHtml += `<a href="#" class="page-link px-4 py-2 text-gray-500 hover:text-brand-dark" data-page="${pagination.page + 1}">&raquo;</a>`;
        }
        paginationHtml += '</nav>';
        container.html(paginationHtml);
    }

    /**
     * Fetches products from the API and renders them.
     */
    function applyFilters() {
        console.log("Applying filters:", currentFilters);
        $('#products-grid').html('<p class="col-span-full text-center text-gray-500">Loading...</p>');

        $.ajax({
            url: 'api/shop/filter.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(currentFilters),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderProducts(response.products);
                    renderPagination(response.pagination);
                    $('#results-count').text(`Showing ${response.products.length} of ${response.pagination.total_products} results`);
                } else {
                    alert('Error: ' + (response.message || 'Could not fetch products.'));
                }
            },
            error: function() {
                alert('An unexpected error occurred while filtering products.');
                $('#products-grid').html('<p class="col-span-full text-center text-red-500">Failed to load products.</p>');
            }
        });
    }

    // --- Event Listeners ---
    
    // Off-Canvas Menu Controls
    $('#open-filter-btn').on('click', function() {
        filterBackdrop.fadeIn(200);
        filterSidebar.removeClass('-translate-x-full');
    });

    $('#close-filter-btn, #filter-backdrop').on('click', function() {
        filterBackdrop.fadeOut(200);
        filterSidebar.addClass('-translate-x-full');
    });

    // Search input with debounce
    $(document).on('keyup', '#search-input', function() {
        clearTimeout(debounceTimer);
        const searchTerm = $(this).val();
        debounceTimer = setTimeout(function() {
            currentFilters.searchTerm = searchTerm;
            currentFilters.page = 1;
            applyFilters();
        }, 500);
    });

    // Category filter
    $(document).on('click', '.filter-category', function(e) {
        e.preventDefault();
        $('.filter-category').removeClass('font-bold text-brand-gold');
        $(this).addClass('font-bold text-brand-gold');
        currentFilters.category = $(this).data('category');
        currentFilters.page = 1;
        applyFilters();
    });

    // Price range slider
    const priceSlider = $('#price-range-slider');
    const priceValue = $('#price-range-value');
    $(document).on('input', '#price-range-slider', function() {
        priceValue.text(`₦${parseInt($(this).val()).toLocaleString()}`);
    });
    $(document).on('change', '#price-range-slider', function() {
        currentFilters.maxPrice = $(this).val();
        currentFilters.page = 1;
        applyFilters();
    });

    // Sorting dropdown
    $('#sort-by-select').on('change', function() {
        currentFilters.sortBy = $(this).val();
        currentFilters.page = 1;
        applyFilters();
    });
    
    // Pagination click handler
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        currentFilters.page = $(this).data('page');
        applyFilters();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    // Clear Filters
    $(document).on('click', '#clear-filters-btn', function() {
        // Reset state object
        currentFilters = {
            searchTerm: '',
            category: 'All',
            maxPrice: 50000,
            sortBy: 'default',
            page: 1
        };
        // Reset form elements
        $('#search-input').val('');
        $('.filter-category').removeClass('font-bold text-brand-gold');
        $('.filter-category[data-category="All"]').addClass('font-bold text-brand-gold');
        priceSlider.val(50000);
        priceValue.text('₦50,000');
        $('#sort-by-select').val('default');
        
        applyFilters();
    });


    // --- Initial Load ---
    applyFilters();
});

