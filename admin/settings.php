<?php include 'includes/header.php'; ?>

<!-- Page Title -->
<h2 class="text-3xl font-bold text-brand-dark mb-6">Site Settings</h2>

<form action="api/settings.php" method="POST">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Site Info & Payment -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Site Information Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Site Information</h3>
                <div class="space-y-4">
                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <input type="email" id="site_email" name="site_email" value="contact@monogramempire.com" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label for="site_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                        <input type="text" id="site_phone" name="site_phone" value="+234 801 234 5678" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label for="site_address" class="block text-sm font-medium text-gray-700">Physical Address</label>
                        <textarea id="site_address" name="site_address" rows="3" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold">123 Fashion Avenue, Victoria Island, Lagos, Nigeria</textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Gateway Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Payment Gateway (Paystack)</h3>
                <div class="space-y-4">
                    <div>
                        <label for="paystack_public_key" class="block text-sm font-medium text-gray-700">Public Key</label>
                        <input type="text" id="paystack_public_key" name="paystack_public_key" value="pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxx" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                    <div>
                        <label for="paystack_secret_key" class="block text-sm font-medium text-gray-700">Secret Key</label>
                        <input type="password" id="paystack_secret_key" name="paystack_secret_key" value="sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxx" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Maintenance & Save -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Maintenance Mode Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold text-brand-dark mb-4 border-b pb-2">Maintenance Mode</h3>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Enable Site Maintenance</span>
                    <label for="maintenance_mode" class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-gold"></div>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">When enabled, only logged-in admins will be able to see the site. Visitors will see a maintenance page.</p>
            </div>
            
            <!-- Save Button -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                 <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-8 rounded-full hover:bg-brand-gray transition-colors">
                    <i class="fas fa-save mr-2"></i>Save All Settings
                </button>
            </div>
        </div>
    </div>
</form>

<?php include 'includes/footer.php'; ?>
