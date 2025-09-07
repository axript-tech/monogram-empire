<?php 
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

// Get token and email from URL and sanitize them
$token = isset($_GET['token']) ? sanitize_input($_GET['token']) : '';
$email = isset($_GET['email']) ? sanitize_input($_GET['email']) : '';

$is_valid_token = false;
$error_message = '';

if (!empty($token) && !empty($email)) {
    // Check if the token is valid and not expired in the database before showing the form
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_token_expires > ?");
    $current_time = time();
    $stmt->bind_param("ssi", $email, $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $is_valid_token = true;
    } else {
        $error_message = "This password reset link is invalid or has expired. Please request a new one.";
    }
    $stmt->close();
} else {
    $error_message = "Invalid password reset link. Please check the URL and try again.";
}

include 'includes/header.php'; 
?>

<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-center text-brand-dark mb-4">Reset Your Password</h2>
            
            <?php if ($is_valid_token): ?>
                <p class="text-center text-gray-500 mb-8">Please enter and confirm your new password below.</p>
                <div id="auth-message-container" class="mb-4" style="display: none;"></div>
                <form id="reset-password-form">
                    <!-- Hidden fields to pass token and email to JavaScript -->
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    
                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700 font-bold mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password" required class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-6">
                        <label for="confirm_new_password" class="block text-gray-700 font-bold mb-2">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-brand-dark text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center">
                             <span class="button-text">Reset Password</span>
                             <i class="fas fa-spinner fa-spin ml-2 button-spinner" style="display: none;"></i>
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
                </div>
                <p class="text-center mt-6">
                    <a href="forgot-password.php" class="text-brand-gold font-bold hover:underline">Request another link</a>
                </p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>
<!-- The auth.js script handles the AJAX submission for this form -->
<script src="assets/js/auth.js"></script>

