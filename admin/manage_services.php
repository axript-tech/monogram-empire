<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Service Request Management</h2>
</div>

<!-- Service Requests Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">Tracking ID</th>
                    <th class="py-2 px-4">Customer</th>
                    <th class="py-2 px-4">Date</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Quote</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody id="services-table-body" class="text-gray-600 text-sm">
                <!-- Service request rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Loading service requests...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-center">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<!-- Service Request Modal (for View/Edit) -->
<div id="service-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
        <button class="close-modal-btn absolute top-4 right-4 text-gray-500 hover:text-gray-800">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <h3 class="text-2xl font-bold text-brand-dark mb-6">Service Request Details</h3>
        
        <form id="service-form">
            <input type="hidden" id="service_id" name="id">

            <!-- Customer Request Details (Read-only) -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Customer Request</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-gray-500">Customer Name</label>
                        <input type="text" id="service_customer_name" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div>
                        <label class="block text-gray-500">Customer Email</label>
                        <input type="text" id="service_customer_email" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div class="col-span-full">
                        <label class="block text-gray-500">Monogram Text</label>
                        <input type="text" id="service_monogram_text" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
                    </div>
                     <div class="col-span-full">
                        <label class="block text-gray-500">Description & Details</label>
                        <textarea id="service_details" readonly rows="4" class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-500">Inspiration File</label>
                        <a href="#" id="service_reference_link" target="_blank" class="mt-1 text-blue-500 hover:underline">View Attached File</a>
                    </div>
                </div>
            </div>

            <!-- Admin Actions (Editable) -->
            <div class="p-4 border rounded-lg">
                 <h4 class="text-lg font-semibold text-gray-800 mb-4">Admin Actions</h4>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="service_status" class="block text-sm font-medium text-gray-700">Request Status</label>
                        <select id="service_status" name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="service_quote" class="block text-sm font-medium text-gray-700">Quote Amount (₦)</label>
                        <input type="number" id="service_quote" name="quote_amount" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                 </div>
                 <div id="converted-product-id-wrapper" class="mt-4 hidden">
                     <label for="converted_product_id" class="block text-sm font-medium text-gray-700">Link to Final Product</label>
                     <input type="text" id="converted_product_id" name="converted_product_id" list="product-datalist" placeholder="Type or select a product by ID/Name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                     <datalist id="product-datalist">
                        <!-- Options will be populated by JavaScript -->
                     </datalist>
                     <p class="text-xs text-gray-500 mt-1">Required when status is 'Completed'. Links this request to the final product.</p>
                 </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" class="close-modal-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-700">Update Request</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

