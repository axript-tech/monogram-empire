<?php
// Monogram Empire - Admin Settings API

// Include necessary files
require_once '../../includes/db_connect.php'; // For potential DB operations
require_once '../../includes/functions.php'; // For helper functions
require_once '../includes/auth_check.php'; // Crucial for security

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- Process Settings Data ---
// In a real application, you would save these settings to a database table
// or a configuration file (e.g., config.json or .env file).

// For this example, we will sanitize the data and simulate the save process.

// Sanitize all incoming POST data
$settings = [];
$settings['site_email'] = isset($_POST['site_email']) ? sanitize_input($_POST['site_email']) : '';
$settings['site_phone'] = isset($_POST['site_phone']) ? sanitize_input($_POST['site_phone']) : '';
$settings['site_address'] = isset($_POST['site_address']) ? sanitize_input($_POST['site_address']) : '';
$settings['paystack_public_key'] = isset($_POST['paystack_public_key']) ? sanitize_input($_POST['paystack_public_key']) : '';
$settings['paystack_secret_key'] = isset($_POST['paystack_secret_key']) ? sanitize_input($_POST['paystack_secret_key']) : '';
$settings['maintenance_mode'] = isset($_POST['maintenance_mode']) ? 'on' : 'off';

// --- Simulation of Saving to a File ---
// Define the path to a configuration file
$configFile = '../../config.json';

// Convert the settings array to a JSON string
$jsonSettings = json_encode($settings, JSON_PRETTY_PRINT);

// Write the settings to the file
// The file_put_contents function will create the file if it doesn't exist.
// if (file_put_contents($configFile, $jsonSettings) !== false) {
//     // Success
// } else {
//     // Error
// }

// --- Log this activity ---
// It's good practice to log important actions like changing settings.
$admin_id = $_SESSION['admin_id'];
$action = "SETTINGS_UPDATE";
$details = "Site settings were updated by admin ID: " . $admin_id;
$ip_address = $_SERVER['REMOTE_ADDR'];

$stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $admin_id, $action, $details, $ip_address);
$stmt->execute();
$stmt->close();
$conn->close();

// For this example, we will always return a success message.
// In a real app, you'd check if the file write was successful.
// We redirect back to the settings page with a success message.
header("Location: ../settings.php?status=success");
exit();

// If this were a full AJAX API, you would use:
// send_json_response(['success' => true, 'message' => 'Settings saved successfully!'], 200);
?>
