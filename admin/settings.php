<?php 
include 'includes/auth_check.php';
include '../includes/db_connect.php'; // Needed to fetch settings

// Fetch current settings to populate the form
$settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

include 'includes/header.php'; 
?>

<!-- Page Title -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-brand-dark">Site Settings</h2>
</div>

<!-- Settings Form -->
<div class="bg-white p-8 rounded-lg shadow-md max-w-3xl mx-auto">
    <form id="settings-form">
        <!-- General Settings -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">General</h3>
            <div class="space-y-4">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                </div>
                <div>
                    <label for="site_email" class="block text-sm font-medium text-gray-700">Default Contact Email</label>
                    <input type="email" id="site_email" name="site_email" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                </div>
            </div>
        </div>

        <!-- Payment Gateway Settings -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Payment Gateway (Paystack)</h3>
             <div class="space-y-4">
                <div>
                    <label for="paystack_public_key" class="block text-sm font-medium text-gray-700">Public Key</label>
                    <input type="text" id="paystack_public_key" name="paystack_public_key" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                </div>
                <div>
                    <label for="paystack_secret_key" class="block text-sm font-medium text-gray-700">Secret Key</label>
                    <input type="password" id="paystack_secret_key" name="paystack_secret_key" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold" placeholder="Enter new key to update">
                </div>
            </div>
        </div>

        <!-- PHPMailer SMTP Settings -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Email (PHPMailer SMTP)</h3>
             <div class="space-y-4">
                <div>
                    <label for="smtp_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="smtp_username" class="block text-sm font-medium text-gray-700">SMTP Username</label>
                        <input type="text" id="smtp_username" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                     <div>
                        <label for="smtp_password" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                        <input type="password" id="smtp_password" name="smtp_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold" placeholder="Enter new password to update">
                    </div>
                 </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                        <label for="smtp_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                    </div>
                     <div>
                        <label for="smtp_secure" class="block text-sm font-medium text-gray-700">Encryption</label>
                         <select id="smtp_secure" name="smtp_secure" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-gold focus:border-brand-gold">
                            <option value="tls" <?= ($settings['smtp_secure'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($settings['smtp_secure'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                        </select>
                    </div>
                 </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-700">Save Settings</button>
        </div>
    </form>
</div>


<?php include 'includes/footer.php'; ?>

