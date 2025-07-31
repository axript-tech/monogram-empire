<?php
// Monogram Empire - Reset Password API

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
$errors = [];
$required_fields = ['token', 'email', 'new_password', 'confirm_new_password'];

foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
}

if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Missing required fields.', 'errors' => $errors], 400);
}

$token = $data['token'];
$email = sanitize_input($data['email']);
$new_password = $data['new_password'];
$confirm_new_password = $data['confirm_new_password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

if (strlen($new_password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($new_password !== $confirm_new_password) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 400);
}

// --- Verify Token ---
// Find the user and token, and check if the token is still valid (not expired).
$stmt = $conn->prepare("
    SELECT pr.user_id 
    FROM password_resets pr
    JOIN users u ON pr.user_id = u.id
    WHERE u.email = ? AND pr.token = ? AND pr.expires_at > NOW()
");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    send_json_response(['success' => false, 'message' => 'Invalid or expired password reset token.'], 400);
}

$reset_request = $result->fetch_assoc();
$user_id = $reset_request['user_id'];
$stmt->close();

// --- Update Password ---
// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Prepare the UPDATE statement for the user's password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    // Password updated successfully. Now, delete the token so it can't be used again.
    $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    send_json_response(['success' => true, 'message' => 'Your password has been reset successfully. You can now log in.'], 200);
} else {
    // Database error during update
    send_json_response(['success' => false, 'message' => 'An error occurred while updating your password. Please try again.'], 500);
}

$stmt->close();
$conn->close();
?>
