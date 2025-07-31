<?php
// Monogram Empire - User Registration API

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
$required_fields = ['first_name', 'last_name', 'email', 'password', 'confirm_password'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
}

// If there are missing fields, stop here
if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Missing required fields.', 'errors' => $errors], 400);
}

// Sanitize inputs
$first_name = sanitize_input($data['first_name']);
$last_name = sanitize_input($data['last_name']);
$email = sanitize_input($data['email']);
$password = $data['password']; // Don't sanitize password before hashing
$confirm_password = $data['confirm_password'];

// More specific validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters long.';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match.';
}

// If there are validation errors, send them back
if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Validation failed.', 'errors' => $errors], 400);
}

// --- Check if user already exists ---
// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    send_json_response(['success' => false, 'message' => 'An account with this email already exists.'], 409); // 409 Conflict
}
$stmt->close();

// --- Create User ---
// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare the INSERT statement
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

// Execute the statement and check for success
if ($stmt->execute()) {
    // On successful registration, you might want to log the user in automatically.
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['user_first_name'] = $first_name;

    send_json_response(['success' => true, 'message' => 'Registration successful! Welcome.'], 201); // 201 Created
} else {
    // In a production environment, you would log this error.
    send_json_response(['success' => false, 'message' => 'An error occurred during registration. Please try again.'], 500);
}

$stmt->close();
$conn->close();
?>
