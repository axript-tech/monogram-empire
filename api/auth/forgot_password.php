<?php
// Monogram Empire - Forgot Password API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    send_json_response(['success' => false, 'message' => 'A valid email is required.'], 400);
}

$email = sanitize_input($data['email']);

// --- Find User ---
// Check if a user with this email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // To prevent user enumeration, we send a generic success message even if the email doesn't exist.
    // The user is told to check their email, but no email is actually sent.
    send_json_response(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.'], 200);
}

$user = $result->fetch_assoc();
$user_id = $user['id'];
$stmt->close();

// --- Generate and Store Reset Token ---
$token = generate_token();
$expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token is valid for 1 hour

// Before inserting a new token, it's good practice to delete any old tokens for this user.
$stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Insert the new token
$stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $token, $expires_at);

if ($stmt->execute()) {
    // --- Simulate Sending Email ---
    // In a real application, you would use a mail library (like PHPMailer) to send an email.
    // For this example, we will construct the link and can return it in the response for testing.
    
    // IMPORTANT: Replace 'http://yourwebsite.com' with your actual domain
    $reset_link = "http://yourwebsite.com/reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($email);

    // --- Mail Sending Logic (Simulated) ---
    // $subject = "Password Reset Request for Monogram Empire";
    // $body = "Please click the following link to reset your password: " . $reset_link;
    // $headers = "From: no-reply@monogramempire.com";
    // mail($email, $subject, $body, $headers); // This would be the actual mail function call

    // Send the generic success response
    send_json_response(['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.'], 200);

} else {
    // Database error
    send_json_response(['success' => false, 'message' => 'An error occurred. Please try again later.'], 500);
}

$stmt->close();
$conn->close();
?>
