<?php 
include 'includes/auth_check.php';
include 'includes/header.php'; 
?>

<!-- Page Title & Add Button -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">User Management</h2>
    <button id="add-user-btn" class="bg-brand-dark text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New User
    </button>
</div>

<!-- Users Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-4">ID</th>
                    <th class="py-2 px-4">Name</th>
                    <th class="py-2 px-4">Email</th>
                    <th class="py-2 px-4">Role</th>
                    <th class="py-2 px-4">Date Joined</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody id="users-table-body" class="text-gray-600 text-sm">
                <!-- User rows will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">Loading users...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-6 flex justify-center">
        <!-- Pagination links will be loaded here -->
    </div>
</div>

<!-- User Modal (for Add/Edit) -->
<div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-lg relative">
        <button class="close-modal-btn absolute top-4 right-4 text-gray-500 hover:text-gray-800">
            <i class="fas fa-times fa-lg"></i>
        </button>
        <h3 class="text-2xl font-bold text-brand-dark mb-6">Add New User</h3>
        <form id="user-form">
            <input type="hidden" id="user_id" name="id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="user_first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="user_first_name" name="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                </div>
                <div>
                    <label for="user_last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="user_last_name" name="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                </div>
            </div>
            <div class="mt-4">
                <label for="user_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="user_email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
            </div>
             <div class="mt-4">
                <label for="user_role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="user_role" name="role" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mt-4">
                <label for="user_password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="user_password" name="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold" placeholder="Required for new users">
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" class="close-modal-btn px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-700">Save User</button>
            </div>
        </form>
    </div>
</div>


<?php include 'includes/footer.php'; ?>

