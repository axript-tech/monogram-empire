<?php include 'includes/header.php'; ?>

<!-- Forgot Password Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-brand-dark" style="font-family: 'Playfair Display', serif;">Forgot Your Password?</h1>
                    <p class="text-gray-600 mt-2">No problem. Enter your email below and we'll send you a reset link.</p>
                </div>

                <!-- Container for AJAX messages -->
                <div id="auth-message-container" class="mb-4" style="display: none;"></div>

                <form id="forgot-password-form" action="api/auth/forgot_password.php" method="POST">
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                            Send Reset Link
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
