<?php
// Monogram Empire - Custom Service Request (Pre-order) API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// A user must be logged in to request a service.
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to request a custom design.'], 401);
}

$user_id = $_SESSION['user_id'];

// --- Validation ---
$required_fields = ['name', 'email', 'initials', 'style_preference', 'details'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        send_json_response(['success' => false, 'message' => 'Please fill out all required fields.'], 400);
    }
}

// Sanitize inputs
$name = sanitize_input($_POST['name']);
$email = sanitize_input($_POST['email']);
$initials = sanitize_input($_POST['initials']);
$style_preference = sanitize_input($_POST['style_preference']);
$details = sanitize_input($_POST['details']);

$full_details = "Customer Name: " . $name . "\n";
$full_details .= "Customer Email: " . $email . "\n";
$full_details .= "Initials: " . $initials . "\n";
$full_details .= "Style Preference: " . $style_preference . "\n\n";
$full_details .= "--- Design Details ---\n" . $details;

// --- File Upload Handling ---
$upload_path = null;
if (isset($_FILES['inspiration_file']) && $_FILES['inspiration_file']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    if (!in_array($_FILES['inspiration_file']['type'], $allowed_types)) {
        send_json_response(['success' => false, 'message' => 'Invalid file type. Please upload a JPG, PNG, or PDF.'], 400);
    }
    if ($_FILES['inspiration_file']['size'] > $max_size) {
        send_json_response(['success' => false, 'message' => 'File is too large. Maximum size is 5 MB.'], 400);
    }

    $upload_dir = '../../uploads/inspirations/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_extension = pathinfo($_FILES['inspiration_file']['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid('inspiration_', true) . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;

    if (move_uploaded_file($_FILES['inspiration_file']['tmp_name'], $upload_path)) {
        // Append file path to details
        $full_details .= "\n\n--- Inspiration File ---\n" . $unique_filename;
    } else {
        send_json_response(['success' => false, 'message' => 'There was an error uploading your file.'], 500);
    }
}

// --- Create Service Request ---
$tracking_id = "ME-CUSTOM-" . strtoupper(substr(md5(uniqid()), 0, 8));
$service_id = 1; // Default service ID

$stmt = $conn->prepare("INSERT INTO service_requests (user_id, service_id, details, tracking_id, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("iiss", $user_id, $service_id, $full_details, $tracking_id);

if ($stmt->execute()) {
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
