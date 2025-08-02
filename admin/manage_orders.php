<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Order Management</h2>

<!-- Order Management Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">All Orders</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="bg-brand-light-gray">
                <tr>
                    <th class="py-3 px-4 font-semibold">Order ID</th>
                    <th class="py-3 px-4 font-semibold">Customer</th>
                    <th class="py-3 px-4 font-semibold">Order Date</th>
                    <th class="py-3 px-4 font-semibold">Total</th>
                    <th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody id="orders-table-body" class="text-gray-600 text-sm">
                <!-- Order data will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                        <p class="mt-2 text-gray-500">Loading orders...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div id="pagination-container" class="mt-6 flex justify-end"></div>
</div>

<!-- View/Edit Order Modal -->
<div id="order-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col">
        <h2 id="order-modal-title" class="text-2xl font-bold text-brand-dark mb-6">Order Details</h2>
        <div class="flex-grow overflow-y-auto pr-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer & Order Info -->
                <div>
                    <div class="mb-4">
                        <h4 class="font-bold text-gray-700">Customer</h4>
                        <p id="order-customer-name" class="text-gray-600"></p>
                        <p id="order-customer-email" class="text-gray-600"></p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-700">Order Summary</h4>
                        <p class="text-gray-600"><strong>Date:</strong> <span id="order-date"></span></p>
                        <p class="text-gray-600"><strong>Total:</strong> <span id="order-total"></span></p>
                        <p class="text-gray-600"><strong>Payment Ref:</strong> <span id="order-payment-ref"></span></p>
                    </div>
                </div>
                <!-- Order Items -->
                <div>
                    <h4 class="font-bold text-gray-700 mb-2">Items Purchased</h4>
                    <div id="order-items-container" class="space-y-2">
                        <!-- Items will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Status Update Form -->
        <form id="order-status-form" class="mt-6 pt-6 border-t">
            <input type="hidden" id="order_id" name="order_id">
            <div class="flex items-center space-x-4">
                <label for="order_status" class="block text-gray-700 font-bold">Update Status:</label>
                <select id="order_status" name="status" required class="flex-grow px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </select>
                <button type="submit" class="bg-brand-dark text-white font-bold py-2 px-6 rounded-full hover:bg-brand-gray transition-colors">Save Status</button>
            </div>
        </form>
        <div class="text-right mt-4">
             <button type="button" id="order-cancel-btn" class="text-gray-600 hover:text-gray-800 font-semibold">Close</button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
