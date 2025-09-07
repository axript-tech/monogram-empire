<?php
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

// Fetch categories for the filter sidebar
$categories = [];
$category_query = "SELECT id, name FROM categories ORDER BY name ASC";
$category_result = $conn->query($category_query);
if ($category_result) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

include 'includes/header.php';
?>

<!-- Off-Canvas Filter Menu for Mobile -->
<div id="filter-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
<aside id="filter-sidebar" class="fixed top-0 left-0 w-80 h-full bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
    <div class="p-6 h-full flex flex-col">
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h3 class="text-xl font-bold text-brand-dark">Filters</h3>
            <button id="close-filter-btn" class="text-gray-500 hover:text-brand-dark">
                <i class="fas fa-times fa-lg"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-grow">
            <?php include 'includes/shop_filters.php'; ?>
        </div>
    </div>
</aside>

<!-- Page Header -->
<div class="bg-gray-100 py-16">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl font-bold text-brand-dark">Our Collection</h1>
        <p class="text-gray-600 mt-2">Discover our curated selection of exquisite monogram designs.</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-6 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
        <!-- Sidebar for Desktop -->
        <aside class="col-span-1 hidden lg:block">
            <?php include 'includes/shop_filters.php'; ?>
        </aside>

        <!-- Product Grid -->
        <main class="col-span-1 lg:col-span-3">
            <!-- Sorting and Results Count -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <div class="w-full flex items-center space-x-4">
                     <!-- Filter Button for Mobile -->
                    <button id="open-filter-btn" class="lg:hidden bg-white border border-gray-300 px-4 py-2 rounded-md flex items-center">
                        <i class="fas fa-filter mr-2"></i> Filters
                    </button>
                    <p id="results-count" class="text-sm text-gray-500">Showing results...</p>
                </div>
                <select id="sort-by-select" class="w-full md:w-auto mt-4 md:mt-0 px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    <option value="default">Default Sorting</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name_asc">Name: A to Z</option>
                    <option value="name_desc">Name: Z to A</option>
                </select>
            </div>

            <!-- Products Grid -->
            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                <!-- Products will be loaded here by JavaScript -->
                <p class="col-span-full text-center text-gray-500">Loading products...</p>
            </div>
            
            <!-- Pagination -->
            <div id="pagination-container" class="mt-12 flex justify-center">
                <!-- Pagination links will be loaded here by JavaScript -->
            </div>
        </main>
    </div>
</div>

<?php 
// To keep the code clean, I've moved the filters to a separate include file.
// This new file is included in both the desktop sidebar and the mobile off-canvas menu.
if (!file_exists('includes/shop_filters.php')) {
    file_put_contents('includes/shop_filters.php', '<?php // This file contains the HTML for the shop filters ?>');
}
// The content for the filter file will be provided in a separate update.

// The footer contains the main jQuery include
include 'includes/footer.php'; 
?>
<!-- Page-specific JavaScript must be loaded AFTER the footer -->
<script src="assets/js/shop.js"></script>

