<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Order Management</h2>
</div>

<!-- Orders Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">Order ID</th>
                    <th class="py-2 px-4">Customer</th>
                    <th class="py-2 px-4">Date</th>
                    <th class="py-2 px-4">Total</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody id="orders-table-body" class="text-gray-600 text-sm">
                <!-- Order rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Loading orders...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-center">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<!-- Order Details Modal -->
<div id="order-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-2xl relative max-h-[90vh] overflow-y-auto">
        <button class="close-modal-btn absolute top-4 right-4 text-gray-500 hover:text-gray-800">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <h3 class="text-2xl font-bold text-brand-dark mb-6">Order Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-sm">
            <div>
                <label class="block text-gray-500">Customer Name</label>
                <input type="text" id="order_customer_name" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-gray-500">Customer Email</label>
                <input type="text" id="order_customer_email" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-gray-500">Order Date</label>
                <input type="text" id="order_date" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-gray-500">Order Total</label>
                <input type="text" id="order_total" readonly class="mt-1 block w-full bg-gray-200 border-gray-300 rounded-md shadow-sm">
            </div>
        </div>

        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-800 mb-2">Items Purchased</h4>
            <ul id="order-items-list" class="list-disc list-inside bg-gray-50 p-4 rounded-md text-sm">
                <!-- Items will be loaded here -->
            </ul>
        </div>

        <form id="order-status-form">
            <input type="hidden" id="order_id" name="id">
            <label for="order_status" class="block text-sm font-medium text-gray-700">Update Order Status</label>
            <div class="mt-2 flex">
                <select id="order_status" name="status" required class="flex-grow block w-full border-gray-300 rounded-l-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-brand-dark text-white rounded-r-md hover:bg-gray-700">Update Status</button>
            </div>
        </form>

        <div class="mt-6 flex justify-end">
            <button type="button" class="close-modal-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Close</button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

