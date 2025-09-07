<!-- Search Filter -->
<div class="mb-8">
    <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Search Designs</h3>
    <div class="relative">
        <input type="text" id="search-input" placeholder="Search by name..." class="w-full pl-4 pr-10 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
        <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>
</div>

<!-- Category Filter -->
<div class="mb-8">
    <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Categories</h3>
    <ul class="space-y-2">
        <li><a href="#" class="filter-category text-gray-600 hover:text-brand-gold font-bold text-brand-gold" data-category="All">All Categories</a></li>
        <?php foreach ($categories as $category) : ?>
            <li><a href="#" class="filter-category text-gray-600 hover:text-brand-gold" data-category="<?= htmlspecialchars($category['name']) ?>"><?= htmlspecialchars($category['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Price Range Filter -->
<div class="mb-8">
    <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Price Range</h3>
    <input type="range" id="price-range-slider" min="500" max="50000" value="50000" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
    <div class="flex justify-between text-sm text-gray-500 mt-2">
        <span>₦500</span>
        <span id="price-range-value">₦50,000</span>
    </div>
</div>

<!-- Clear Filters Button -->
<div>
    <button id="clear-filters-btn" class="w-full bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
        Clear All Filters
    </button>
</div>
