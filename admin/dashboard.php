<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Admin Dashboard</h2>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Total Revenue Card -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p id="stat-total-revenue" class="text-3xl font-bold text-brand-dark">...</p>
        </div>
        <div class="text-4xl text-brand-gold opacity-50">
            <i class="fas fa-dollar-sign"></i>
        </div>
    </div>
    <!-- Total Orders Card -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Total Orders</p>
            <p id="stat-total-orders" class="text-3xl font-bold text-brand-dark">...</p>
        </div>
        <div class="text-4xl text-brand-gold opacity-50">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </div>
    <!-- Total Users Card -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Total Users</p>
            <p id="stat-total-users" class="text-3xl font-bold text-brand-dark">...</p>
        </div>
        <div class="text-4xl text-brand-gold opacity-50">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <!-- Pending Requests Card -->
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Pending Requests</p>
            <p id="stat-pending-requests" class="text-3xl font-bold text-brand-dark">...</p>
        </div>
        <div class="text-4xl text-brand-gold opacity-50">
            <i class="fas fa-concierge-bell"></i>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="mt-8 bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-bold text-brand-dark mb-4">Recent Activity</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">Admin</th>
                    <th class="py-2 px-4">Action</th>
                    <th class="py-2 px-4">Details</th>
                    <th class="py-2 px-4">Date</th>
                </tr>
            </thead>
            <tbody id="recent-activity-body" class="text-gray-600 text-sm">
                <!-- Activity will be loaded here dynamically -->
                <tr>
                    <td colspan="4" class="text-center py-4">Loading recent activity...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

