<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Payment Management</h2>
</div>

<!-- Payments Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">ID</th>
                    <th class="py-2 px-4">Reference</th>
                    <th class="py-2 px-4">Customer</th>
                    <th class="py-2 px-4">Amount</th>
                    <th class="py-2 px-4">Type</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Date</th>
                </tr>
            </thead>
            <tbody id="payments-table-body" class="text-gray-600 text-sm">
                <!-- Payment rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">Loading payments...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-center">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<?php include 'includes/footer.php'; ?>

