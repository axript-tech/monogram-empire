<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Activity Log</h2>

<!-- Activity Log Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-brand-dark">Recent Admin Activities</h3>
        <!-- Filter/Search can be added here -->
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
            <tbody class="text-gray-600 text-sm">
                <!-- Placeholder Rows -->
                <tr class="border-b">
                    <td class="py-3 px-4">101</td>
                    <td class="py-3 px-4">admin@monogramempire.com</td>
                    <td class="py-3 px-4"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">ORDER_UPDATE</span></td>
                    <td class="py-3 px-4">Status for order #ME-10521 changed to 'Completed'.</td>
                    <td class="py-3 px-4">192.168.1.1</td>
                    <td class="py-3 px-4">2025-07-28 10:30:15</td>
                </tr>
                <tr class="border-b bg-gray-50">
                    <td class="py-3 px-4">100</td>
                    <td class="py-3 px-4">admin@monogramempire.com</td>
                    <td class="py-3 px-4"><span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">PRODUCT_EDIT</span></td>
                    <td class="py-3 px-4">Price for product "Victorian Crest" updated.</td>
                    <td class="py-3 px-4">192.168.1.1</td>
                    <td class="py-3 px-4">2025-07-28 09:15:42</td>
                </tr>
                 <tr class="border-b">
                    <td class="py-3 px-4">99</td>
                    <td class="py-3 px-4">admin@monogramempire.com</td>
                    <td class="py-3 px-4"><span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded">SERVICE_UPDATE</span></td>
                    <td class="py-3 px-4">Quote of &#8358;50,000 sent for request #ME-CUSTOM-1234.</td>
                    <td class="py-3 px-4">192.168.1.1</td>
                    <td class="py-3 px-4">2025-07-27 18:05:11</td>
                </tr>
                 <tr class="border-b bg-gray-50">
                    <td class="py-3 px-4">98</td>
                    <td class="py-3 px-4">admin@monogramempire.com</td>
                    <td class="py-3 px-4"><span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">ADMIN_LOGIN</span></td>
                    <td class="py-3 px-4">Admin user logged in successfully.</td>
                    <td class="py-3 px-4">192.168.1.1</td>
                    <td class="py-3 px-4">2025-07-27 17:55:01</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="mt-6 flex justify-end">
        <nav class="flex items-center space-x-2">
            <a href="#" class="px-4 py-2 text-gray-500 hover:text-brand-dark">&laquo;</a>
            <a href="#" class="px-4 py-2 text-white bg-brand-dark rounded-md">1</a>
            <a href="#" class="px-4 py-2 text-gray-700 hover:bg-gray-200 rounded-md">2</a>
            <a href="#" class="px-4 py-2 text-gray-500 hover:text-brand-dark">&raquo;</a>
        </nav>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
