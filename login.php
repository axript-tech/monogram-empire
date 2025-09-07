<?php 
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include 'includes/header.php'; 

// Redirect logged-in users to their history page
if (is_logged_in()) {
    redirect('order-history.php');
}
?>

<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-center text-brand-dark mb-2">Welcome Back!</h2>
            <p class="text-center text-gray-500 mb-8">Log in to access your account and order history.</p>

            <!-- Container for AJAX messages -->
            <div id="auth-message-container" class="mb-4" style="display: none;"></div>

            <form id="login-form">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                </div>
                <div class="flex items-center justify-between mb-6">
                    <a href="forgot-password.php" class="text-sm text-brand-gold hover:underline">Forgot Password?</a>
                </div>
                <div>
                    <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center">
                        <span class="button-text">Log In</span>
                        <i class="fas fa-spinner fa-spin ml-2 button-spinner" style="display: none;"></i>
                    </button>
                </div>
            </form>
            <p class="text-center text-gray-500 mt-6">
                Don't have an account? <a href="register.php" class="text-brand-gold font-bold hover:underline">Sign Up</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<!-- Page-specific JavaScript -->
<script src="assets/js/auth.js"></script>

