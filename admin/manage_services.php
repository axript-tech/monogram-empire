<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Service Request Management</h2>

<!-- Service Request Management Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">All Custom Requests</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="bg-brand-light-gray">
                <tr>
                    <th class="py-3 px-4 font-semibold">Tracking ID</th>
                    <th class="py-3 px-4 font-semibold">Customer</th>
                    <th class="py-3 px-4 font-semibold">Request Date</th>
                    <th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold">Quote</th>
                    <th class="py-3 px-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody id="services-table-body" class="text-gray-600 text-sm">
                <!-- Service request data will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                        <p class="mt-2 text-gray-500">Loading requests...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div id="pagination-container" class="mt-6 flex justify-end"></div>
</div>

<!-- View/Edit Service Request Modal -->
<div id="service-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
        <h2 id="service-modal-title" class="text-2xl font-bold text-brand-dark mb-6">Service Request Details</h2>
        <form id="service-form">
            <input type="hidden" id="request_id" name="request_id">
            
            <div class="mb-4 bg-gray-100 p-4 rounded-md">
                <h4 class="font-bold text-gray-700">Request Details:</h4>
                <p id="service-details" class="text-sm text-gray-600 whitespace-pre-wrap max-h-48 overflow-y-auto"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
                    <select id="status" name="status" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="awaiting_payment">Awaiting Payment</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="quote_price" class="block text-gray-700 font-bold mb-2">Quote Price (&#8358;)</label>
                    <input type="number" id="quote_price" name="quote_price" step="0.01" placeholder="e.g., 50000" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="service-cancel-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-full hover:bg-gray-400 transition-colors">Close</button>
                <button type="submit" class="bg-brand-gold text-brand-dark font-bold py-2 px-6 rounded-full hover:bg-yellow-300 transition-colors">Update Request</button>
            </div>
        </form>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
