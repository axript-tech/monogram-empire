<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Payment Management</h2>

<!-- Payment Management Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">All Transactions</h3>
        <!-- Filter/Search can be added here -->
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="bg-brand-light-gray">
                <tr>
                    <th class="py-3 px-4 font-semibold">Transaction ID</th>
                    <th class="py-3 px-4 font-semibold">Reference</th>
                    <th class="py-3 px-4 font-semibold">Type</th>
                    <th class="py-3 px-4 font-semibold">Customer</th>
                    <th class="py-3 px-4 font-semibold">Amount</th>
                    <th class="py-3 px-4 font-semibold">Status</th>
                    <th class="py-3 px-4 font-semibold">Date</th>
                </tr>
            </thead>
            <tbody id="payments-table-body" class="text-gray-600 text-sm">
                <!-- Payment data will be loaded here by JavaScript -->
                <tr>
                    <td colspan="7" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                        <p class="mt-2 text-gray-500">Loading payments...</p>
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
