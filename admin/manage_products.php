<?php 
include 'includes/header.php'; 
include '../includes/db_connect.php';

// Fetch categories for the modal dropdown
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Product Management</h2>

<!-- Product Management Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">All Products</h3>
        <button id="add-product-btn" class="bg-brand-gold text-brand-dark font-bold py-2 px-4 rounded-full hover:bg-yellow-300 transition-colors">
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
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div id="pagination-container" class="mt-6 flex justify-end"></div>
</div>

<!-- Add/Edit Product Modal -->
<div id="product-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <h2 id="product-modal-title" class="text-2xl font-bold text-brand-dark mb-6">Add New Product</h2>
        <form id="product-form" enctype="multipart/form-data" class="flex-grow overflow-y-auto pr-4 custom-scrollbar">
            <input type="hidden" id="product_id" name="product_id">
            <div class="space-y-4">
                <div>
                    <label for="product_name" class="block text-gray-700 font-bold mb-2">Product Name</label>
                    <input type="text" id="product_name" name="name" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-gray-700 font-bold mb-2">Category</label>
                        <select id="category_id" name="category_id" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="price" class="block text-gray-700 font-bold mb-2">Price (&#8358;)</label>
                        <input type="number" id="price" name="price" step="0.01" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                    <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold"></textarea>
                </div>
                
                <!-- Image Previews (for editing) -->
                <div id="product-image-previews" class="hidden space-y-2">
                    <label class="block text-gray-700 font-bold">Current Images</label>
                    <div id="preview-container" class="grid grid-cols-5 gap-2">
                        <!-- Previews will be inserted here by JS -->
                    </div>
                     <p class="text-xs text-gray-500 mt-1">To replace an image, simply upload a new file in the corresponding slot below.</p>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2">Upload Product Images</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="file" name="image_url" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-brand-gold file:text-brand-dark hover:file:bg-yellow-300">
                        <input type="file" name="image_url_2" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                        <input type="file" name="image_url_3" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                        <input type="file" name="image_url_4" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                        <input type="file" name="image_url_5" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                    </div>
                     <p class="text-xs text-gray-500 mt-1">The first image is the main display image.</p>
                </div>
                 <div>
                    <label for="digital_file_url" class="block text-gray-700 font-bold mb-2">Digital File (ZIP)</label>
                    <input type="file" id="digital_file_url" name="digital_file_url" accept=".zip" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-brand-gold file:text-brand-dark hover:file:bg-yellow-300">
                </div>
            </div>
            <div class="flex justify-end space-x-4 mt-6 pt-4 border-t">
                <button type="button" id="product-cancel-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-full hover:bg-gray-400 transition-colors">Cancel</button>
                <button type="submit" class="bg-brand-gold text-brand-dark font-bold py-2 px-6 rounded-full hover:bg-yellow-300 transition-colors">Save Product</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
