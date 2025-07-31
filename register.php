<?php include 'includes/header.php'; ?>

<!-- Registration Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 flex justify-center">
        <div class="w-full max-w-lg">
            <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-brand-dark" style="font-family: 'Playfair Display', serif;">Create Your Account</h1>
                    <p class="text-gray-600 mt-2">Join the Empire to save your designs and track your orders.</p>
                </div>

                <!-- Container for AJAX messages -->
                <div id="auth-message-container" class="mb-4" style="display: none;"></div>

                <form id="register-form" action="api/auth/register.php" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="first_name" class="block text-gray-700 font-bold mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" placeholder="John" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                        </div>
                        <div>
                            <label for="last_name" class="block text-gray-700 font-bold mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" placeholder="Doe" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-brand-gold focus:ring-brand-gold border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">I agree to the <a href="terms.php" class="text-brand-gold hover:underline">Terms & Conditions</a></span>
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? <a href="login.php" class="font-bold text-brand-gold hover:underline">Log In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
