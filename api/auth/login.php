<?php
// Monogram Empire - User Login API

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

// Check for required fields
if (empty($data['email'])) {
    $errors[] = 'Email is required.';
}
if (empty($data['password'])) {
    $errors[] = 'Password is required.';
}

if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Missing required fields.', 'errors' => $errors], 400);
}

// Sanitize email
$email = sanitize_input($data['email']);
$password = $data['password'];

// --- Authenticate User ---
// Use a prepared statement to find the user by email
$stmt = $conn->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // User found, now verify the password
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        // Password is correct, authentication successful.
        // Regenerate session ID to prevent session fixation attacks.
        session_regenerate_id(true);

        // Store user data in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_first_name'] = $user['first_name'];
        
        $stmt->close();
        $conn->close();

        // Send success response
        send_json_response(['success' => true, 'message' => 'Login successful! Welcome back.'], 200);
    } else {
        // Password is not correct
        send_json_response(['success' => false, 'message' => 'Invalid email or password.'], 401); // 401 Unauthorized
    }
} else {
    // No user found with that email
    send_json_response(['success' => false, 'message' => 'Invalid email or password.'], 401); // 401 Unauthorized
}

$stmt->close();
$conn->close();
?>
