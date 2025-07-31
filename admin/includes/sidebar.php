<!-- Sidebar -->
<div class="w-64 bg-brand-dark text-white p-4 flex flex-col h-screen">
    <div class="text-center mb-8">
        <a href="dashboard.php" class="text-2xl font-bold">
            <span class="text-brand-gold">Monogram</span>Empire
        </a>
        <p class="text-xs text-gray-400">ADMIN PANEL</p>
    </div>
    <nav class="overflow-y-auto custom-scrollbar">
        <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-tachometer-alt w-6"></i>
            <span class="mx-4">Dashboard</span>
        </a>
        <a href="manage_users.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-users w-6"></i>
            <span class="mx-4">User Management</span>
        </a>
        <a href="manage_products.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-box w-6"></i>
            <span class="mx-4">Product Management</span>
        </a>
        <a href="manage_services.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-concierge-bell w-6"></i>
            <span class="mx-4">Service Management</span>
        </a>
        <a href="manage_orders.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-shopping-cart w-6"></i>
            <span class="mx-4">Order Management</span>
        </a>
        <a href="manage_payments.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-credit-card w-6"></i>
            <span class="mx-4">Payment Management</span>
        </a>
        <a href="activity_log.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-history w-6"></i>
            <span class="mx-4">Activity Log</span>
        </a>
        <a href="settings.php" class="flex items-center mt-4 px-4 py-2 text-gray-300 hover:bg-brand-gray hover:text-white rounded-md">
            <i class="fas fa-cog w-6"></i>
            <span class="mx-4">Settings</span>
        </a>
    </nav>
    <div class="mt-auto pt-4 text-center text-xs text-gray-500">
        &copy; <?php echo date("Y"); ?> Monogram Empire
    </div>
</div>
