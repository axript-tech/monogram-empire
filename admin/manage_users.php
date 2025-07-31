<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">User Management</h2>

<!-- User Management Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">All Users</h3>
        <button id="add-user-btn" class="bg-brand-gold text-brand-dark font-bold py-2 px-4 rounded-full hover:bg-yellow-300 transition-colors">
            <i class="fas fa-plus mr-2"></i>Add New User
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="bg-brand-light-gray">
                <tr>
                    <th class="py-3 px-4 font-semibold">User ID</th>
                    <th class="py-3 px-4 font-semibold">Name</th>
                    <th class="py-3 px-4 font-semibold">Email</th>
                    <th class="py-3 px-4 font-semibold">Role</th>
                    <th class="py-3 px-4 font-semibold">Joined Date</th>
                    <th class="py-3 px-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody id="users-table-body" class="text-gray-600 text-sm">
                <!-- User data will be loaded here by JavaScript -->
                <tr>
                    <td colspan="6" class="text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-300"></i>
                        <p class="mt-2 text-gray-500">Loading users...</p>
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

<!-- Add/Edit User Modal -->
<div id="user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 id="modal-title" class="text-2xl font-bold text-brand-dark mb-6">Add New User</h2>
        <form id="user-form">
            <input type="hidden" id="user_id" name="user_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="first_name" class="block text-gray-700 font-bold mb-2">First Name</label>
                    <input type="text" id="first_name" name="first_name" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div>
                    <label for="last_name" class="block text-gray-700 font-bold mb-2">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (when editing).</p>
            </div>
            <div class="mb-6">
                <label for="role" class="block text-gray-700 font-bold mb-2">Role</label>
                <select id="role" name="role" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" id="cancel-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-full hover:bg-gray-400 transition-colors">Cancel</button>
                <button type="submit" class="bg-brand-gold text-brand-dark font-bold py-2 px-6 rounded-full hover:bg-yellow-300 transition-colors">Save User</button>
            </div>
        </form>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
