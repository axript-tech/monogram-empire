<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Activity Log</h2>

<!-- Activity Log Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">Recent Admin Activities</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="bg-brand-light-gray">
                <tr>
                    <th class="py-3 px-4 font-semibold">Log ID</th>
                    <th class="py-3 px-4 font-semibold">Admin User</th>
                    <th class="py-3 px-4 font-semibold">Action</th>
                    <th class="py-3 px-4 font-semibold">Details</th>
                    <th class="py-3 px-4 font-semibold">IP Address</th>
                    <th class="py-3 px-4 font-semibold">Timestamp</th>
                </tr>
            </thead>
            <tbody id="activity-log-table-body" class="text-gray-600 text-sm">
                <!-- Log data will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                        <p class="mt-2 text-gray-500">Loading activity log...</p>
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
