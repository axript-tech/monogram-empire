<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Product Management</h2>

<!-- Product Management Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">All Products</h3>
        <button class="bg-brand-gold text-brand-dark font-bold py-2 px-4 rounded-full hover:bg-yellow-300 transition-colors">
            <i class="fas fa-plus mr-2"></i>Add New Product
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="bg-brand-light-gray">
                <tr>
                    <th class="py-3 px-4 font-semibold">Image</th>
                    <th class="py-3 px-4 font-semibold">Product ID</th>
                    <th class="py-3 px-4 font-semibold">Name</th>
                    <th class="py-3 px-4 font-semibold">Category</th>
                    <th class="py-3 px-4 font-semibold">Price</th>
                    <th class="py-3 px-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody id="products-table-body" class="text-gray-600 text-sm">
                <!-- Product data will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                        <p class="mt-2 text-gray-500">Loading products...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div id="pagination-container" class="mt-6 flex justify-end">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<?php include 'includes/footer.php'; ?>

