<?php
// Monogram Empire - Custom Service Request (Pre-order) API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
// A user must be logged in to request a service.
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to request a custom design.'], 401);
}

$user_id = $_SESSION['user_id'];

// --- Validation ---
// We are dealing with form data (multipart/form-data) here, so we use $_POST.
$errors = [];
$required_fields = ['name', 'email', 'initials', 'style_preference', 'details'];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
}

if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Missing required fields.', 'errors' => $errors], 400);
}

// Sanitize inputs
$name = sanitize_input($_POST['name']);
$email = sanitize_input($_POST['email']);
$initials = sanitize_input($_POST['initials']);
$style_preference = sanitize_input($_POST['style_preference']);
$details = sanitize_input($_POST['details']);

// Combine all details into a single text block for the database
$full_details = "Initials: " . $initials . "\n";
$full_details .= "Style Preference: " . $style_preference . "\n\n";
$full_details .= "--- Design Details ---\n" . $details;

// --- File Upload Simulation ---
// In a real application, you would handle file uploads securely:
// 1. Check file types and sizes.
// 2. Generate unique filenames.
// 3. Move uploaded files to a secure, non-public directory.
// 4. Store the file paths in the database.
// For now, we will just acknowledge if files were sent.
if (!empty($_FILES['inspiration_files'])) {
    $file_count = count($_FILES['inspiration_files']['name']);
    $full_details .= "\n\n--- User uploaded " . $file_count . " inspiration file(s). ---";
}

// --- Create Service Request ---
// Generate a unique tracking ID
$tracking_id = "ME-CUSTOM-" . strtoupper(substr(md5(uniqid()), 0, 8));

// For now, we'll assume a default service_id (e.g., 1 for 'Custom Monogram Design')
// In a more complex app, the user might select this from a list.
$service_id = 1; 

$stmt = $conn->prepare("INSERT INTO service_requests (user_id, service_id, details, tracking_id, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("iiss", $user_id, $service_id, $full_details, $tracking_id);

if ($stmt->execute()) {
    // --- Simulate Email Notification to Admin ---
    // You would send an email to the admin here to notify them of the new request.
    
    send_json_response([
        'success' => true, 
        'message' => 'Your custom design request has been submitted successfully! You will receive a quote within 2-3 business days.',
        'tracking_id' => $tracking_id
    ], 201);
} else {
    send_json_response(['success' => false, 'message' => 'Failed to submit your request. Please try again.'], 500);
}

$stmt->close();
$conn->close();
?>
