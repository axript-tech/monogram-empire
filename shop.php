<?php 
include 'includes/header.php'; 
include 'includes/db_connect.php';

// Fetch categories from the database for the filter sidebar
$categories_query = "SELECT name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result->num_rows > 0) {
    while($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['name'];
    }
}
$conn->close();
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Our Designs</h1>
        <p class="text-lg text-gray-300 mt-2">Explore our curated collection of exquisite monogram templates.</p>
    </div>
</div>

<!-- Shop Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row gap-8">

            <!-- Filters Sidebar -->
            <aside class="w-full md:w-1/4 lg:w-1/5">
                <div class="p-6 bg-brand-light-gray rounded-lg shadow-sm sticky top-24">
                    <h2 class="text-xl font-bold text-brand-dark mb-6">Filters</h2>

                    <!-- Category Filter -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-brand-dark mb-3">Categories</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li><a href="#" class="filter-category font-bold text-brand-gold" data-category="All">All</a></li>
                            <?php foreach ($categories as $category): ?>
                                <li><a href="#" class="filter-category hover:text-brand-gold" data-category="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Price Range Filter -->
                    <div>
                        <h3 class="font-semibold text-brand-dark mb-4">Price Range</h3>
                        <input type="range" id="price-range-slider" min="5000" max="50000" value="50000" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-sm text-gray-500 mt-2">
                            <span>&#8358;5k</span>
                            <span id="price-range-value">&#8358;50k</span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <main class="w-full md:w-3/4 lg:w-4/5">
                <!-- Sorting and View Options -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <p id="results-count" class="text-gray-600">Loading products...</p>
                    <select id="sort-by-select" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-brand-gold">
                        <option value="default">Default sorting</option>
                        <option value="price_asc">Sort by price: low to high</option>
                        <option value="price_desc">Sort by price: high to low</option>
                        <option value="latest">Sort by latest</option>
                    </select>
                </div>

                <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Products will be loaded here by JavaScript -->
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="mt-12 flex justify-center">
                    <!-- Pagination links will be loaded here if implemented -->
                </div>
            </main>
        </div>
    </div>
</section>

<!-- IMPORTANT: Include the page-specific JavaScript file -->

<?php include 'includes/footer.php'; ?>
