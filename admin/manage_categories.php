<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title & Add Button -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Category Management</h2>
    <button id="add-category-btn" class="bg-brand-dark text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New Category
    </button>
</div>

<!-- Categories Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">ID</th>
                    <th class="py-2 px-4">Category Name</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody id="categories-table-body" class="text-gray-600 text-sm">
                <!-- Category rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="3" class="text-center py-8 text-gray-500">Loading categories...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-center">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<!-- Category Modal (for Add/Edit) -->
<div id="category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-lg relative">
        <button class="close-modal-btn absolute top-4 right-4 text-gray-500 hover:text-gray-800">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <h3 id="category-modal-title" class="text-2xl font-bold text-brand-dark mb-6">Add New Category</h3>
        <form id="category-form">
            <input type="hidden" id="category_id" name="id">
            <div>
                <label for="category_name" class="block text-sm font-medium text-gray-700">Category Name</label>
                <input type="text" id="category_name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" class="close-modal-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-700">Save Category</button>
            </div>
        </form>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
