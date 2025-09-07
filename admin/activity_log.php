<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Activity Log</h2>
</div>

<!-- Activity Log Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">Date & Time</th>
                    <th class="py-2 px-4">Admin</th>
                    <th class="py-2 px-4">Action</th>
                    <th class="py-2 px-4">Details</th>
                    <th class="py-2 px-4">IP Address</th>
                </tr>
            </thead>
            <tbody id="activity-log-table-body" class="text-gray-600 text-sm">
                <!-- Log rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">Loading activity log...</td>
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

