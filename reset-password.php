<?php include 'includes/header.php'; ?>

<!-- Reset Password Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-brand-dark" style="font-family: 'Playfair Display', serif;">Set a New Password</h1>
                    <p class="text-gray-600 mt-2">Please enter and confirm your new password below.</p>
                </div>

                <form id="reset-password-form" action="api/auth/reset_password.php" method="POST">
                    <!-- Hidden fields for token and email, which would be populated from the URL query parameters -->
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">

                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700 font-bold mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="••••••••" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-6">
                        <label for="confirm_new_password" class="block text-gray-700 font-bold mb-2">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="••••••••" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                            Reset Password
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Remembered your password? <a href="login.php" class="font-bold text-brand-gold hover:underline">Back to Log In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
