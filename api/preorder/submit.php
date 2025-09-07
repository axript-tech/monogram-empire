<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to submit a request.'], 401);
}

// --- Validation ---
$errors = [];
if (empty($_POST['first_name'])) $errors[] = "First name is required.";
if (empty($_POST['last_name'])) $errors[] = "Last name is required.";
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
if (empty($_POST['monogram_text'])) $errors[] = "Text for the monogram is required.";
if (empty($_POST['style_preference'])) $errors[] = "Style preference is required.";

if (!empty($errors)) {
    send_json_response(['success' => false, 'message' => 'Please correct the errors.', 'errors' => $errors], 400);
}

// --- File Upload Handling (Optional) ---
$file_path = null;
if (isset($_FILES['inspiration_file']) && $_FILES['inspiration_file']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../../uploads/references/";
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
             send_json_response(['success' => false, 'message' => 'Could not create upload directory. Please check permissions.'], 500);
        }
    }
    
    $file_info = pathinfo($_FILES["inspiration_file"]["name"]);
    $image_file_type = strtolower($file_info['extension']);
    $unique_filename = uniqid('ref_', true) . '.' . $image_file_type;
    $target_file = $target_dir . $unique_filename;

    $check = getimagesize($_FILES["inspiration_file"]["tmp_name"]);
    if ($check === false) {
        send_json_response(['success' => false, 'message' => 'Inspiration file is not a valid image.'], 400);
    }
    if ($_FILES["inspiration_file"]["size"] > 2000000) { // 2MB limit
        send_json_response(['success' => false, 'message' => 'Inspiration file is too large (2MB limit).'], 400);
    }
    $allowed_formats = ["jpg", "png", "jpeg", "gif"];
    if (!in_array($image_file_type, $allowed_formats)) {
        send_json_response(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.'], 400);
    }

    if (move_uploaded_file($_FILES["inspiration_file"]["tmp_name"], $target_file)) {
        $file_path = 'uploads/references/' . $unique_filename;
    } else {
        send_json_response(['success' => false, 'message' => 'There was an error uploading your file.'], 500);
    }
}

// --- Database Insertion ---
$user_id = $_SESSION['user_id'];
$design_name = sanitize_input($_POST['monogram_text']);
$description = "Style: " . sanitize_input($_POST['style_preference']) . "\nDetails: " . sanitize_input($_POST['additional_details']);
$tracking_id = 'ME-' . strtoupper(substr(uniqid(), -8));

$stmt = $conn->prepare("INSERT INTO service_requests (user_id, design_name, description, reference_image_path, tracking_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $design_name, $description, $file_path, $tracking_id);

if ($stmt->execute()) {
    send_json_response(['success' => true, 'message' => 'Your request has been submitted successfully!', 'tracking_id' => $tracking_id]);
} else {
    send_json_response(['success' => false, 'message' => 'Database error. Please try again.'], 500);
}

$stmt->close();
$conn->close();

