<?php 
include 'includes/auth_check.php';
include '../includes/db_connect.php'; // Needed to fetch categories

// Fetch categories for the modal dropdown
$categories = [];
$category_query = "SELECT id, name FROM categories ORDER BY name ASC";
$category_result = $conn->query($category_query);
if ($category_result) {
    $categories = $category_result->fetch_all(MYSQLI_ASSOC);
}

include 'includes/header.php'; 
?>

<!-- Page Title & Add Button -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Product Management</h2>
    <button id="add-product-btn" class="bg-brand-dark text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New Product
    </button>
</div>

<!-- Products Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">Image</th>
                    <th class="py-2 px-4">Name</th>
                    <th class="py-2 px-4">Category</th>
                    <th class="py-2 px-4">Price</th>
                    <th class="py-2 px-4">Date Added</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody id="products-table-body" class="text-gray-600 text-sm">
                <!-- Product rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Loading products...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-center">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<!-- Product Modal (for Add/Edit) - Redesigned with Steps -->
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-3xl relative max-h-[90vh] flex flex-col">
        <button class="close-modal-btn absolute top-4 right-4 text-gray-500 hover:text-gray-800">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <h3 id="product-modal-title" class="text-2xl font-bold text-brand-dark mb-4">Add New Product</h3>
        
        <!-- Step Indicator -->
        <div class="mb-6 border-b pb-4">
            <div class="flex justify-between">
                <div class="step-indicator flex-1 text-center" data-step="1">
                    <span class="step-circle active">1</span>
                    <p class="step-text active">Core Details</p>
                </div>
                <div class="step-indicator flex-1 text-center" data-step="2">
                    <span class="step-circle">2</span>
                    <p class="step-text">Images</p>
                </div>
                <div class="step-indicator flex-1 text-center" data-step="3">
                    <span class="step-circle">3</span>
                    <p class="step-text">Digital File</p>
                </div>
            </div>
        </div>

        <div id="product-modal-loader" class="text-center py-12 hidden">
             <i class="fas fa-spinner fa-spin text-4xl text-brand-gold"></i>
             <p class="mt-2">Loading product details...</p>
        </div>

        <form id="product-form" class="flex-grow overflow-y-auto" enctype="multipart/form-data">
            <input type="hidden" id="product_id" name="id">

            <!-- Step 1: Core Details -->
            <div class="form-step" data-step="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" id="product_name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label for="product_category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="product_category" name="category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="product_price" class="block text-sm font-medium text-gray-700">Price (₦)</label>
                        <input type="number" id="product_price" name="price" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                     <div>
                        <label for="product_sku" class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" id="product_sku" name="sku" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold" placeholder="Auto-generated if left blank">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="product_description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="product_description" name="description" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold"></textarea>
                </div>
            </div>

            <!-- Step 2: Images -->
            <div class="form-step hidden" data-step="2">
                 <h4 class="text-md font-semibold text-gray-800 mb-2">Product Images</h4>
                 <p class="text-sm text-gray-500 mb-4">Upload a main image and up to four additional gallery images.</p>
                 <div id="image-previews" class="hidden grid grid-cols-3 sm:grid-cols-5 gap-4 mb-4">
                    <!-- Image previews will be loaded here -->
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700">Main Image</label>
                        <div class="mt-1 flex items-center">
                            <div class="file-input-wrapper">
                                 <span class="file-input-button"><i class="fas fa-upload mr-2"></i>Choose File</span>
                                 <input type="file" name="image_url_1" data-filename-target="#filename-1" data-preview-target="#preview-1" accept="image/*">
                            </div>
                            <img id="preview-1" class="w-16 h-16 object-cover rounded-md ml-4 hidden">
                        </div>
                        <span id="filename-1" class="file-input-filename">No file chosen</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php for ($i = 2; $i <= 5; $i++): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Image <?= $i ?></label>
                             <div class="mt-1 flex items-center">
                                <div class="file-input-wrapper">
                                     <span class="file-input-button"><i class="fas fa-upload mr-2"></i>Choose File</span>
                                     <input type="file" name="image_url_<?= $i ?>" data-filename-target="#filename-<?= $i ?>" data-preview-target="#preview-<?= $i ?>" accept="image/*">
                                </div>
                                <img id="preview-<?= $i ?>" class="w-16 h-16 object-cover rounded-md ml-4 hidden">
                            </div>
                            <span id="filename-<?= $i ?>" class="file-input-filename">No file chosen</span>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Digital File -->
            <div class="form-step hidden" data-step="3">
                <h4 class="text-md font-semibold text-gray-800 mb-2">Downloadable File</h4>
                <p class="text-sm text-gray-500 mb-4">Upload the final product file that customers will receive after purchase.</p>
                 <div class="bg-gray-50 p-6 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700">Product File</label>
                     <div class="mt-1 file-input-wrapper">
                         <span class="file-input-button"><i class="fas fa-file-archive mr-2"></i>Choose Digital File</span>
                         <input type="file" name="digital_file" data-filename-target="#filename-digital" accept=".zip">
                         <span id="filename-digital" class="file-input-filename">No file chosen</span>
                    </div>
                     <p class="text-xs text-gray-500 mt-2">Required for new products. Leave blank when editing to keep the existing file.</p>
                </div>
            </div>
            
        </form>
        
        <!-- Navigation -->
        <div class="mt-6 pt-4 border-t flex justify-between items-center">
            <button type="button" id="prev-step-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 hidden">Previous</button>
            <div class="flex-grow"></div> <!-- Spacer -->
            <button type="button" id="next-step-btn" class="px-4 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-700">Next</button>
            <button type="submit" form="product-form" id="submit-product-btn" class="px-4 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-700 hidden">Save Product</button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

