<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_admin()) {
    //send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];

// --- Handle GET requests (Fetch current settings) ---
if ($method === 'GET') {
    $settings = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    send_json_response(['success' => true, 'settings' => $settings]);
}

// --- Handle POST requests (Update settings) ---
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        send_json_response(['success' => false, 'message' => 'No data received.'], 400);
    }
    
    $conn->begin_transaction();
    $success = true;

    try {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        
        // Define keys that are passwords and should not be updated if empty
        $password_keys = ['paystack_secret_key', 'smtp_password'];

        foreach ($data as $key => $value) {
            // Skip updating password fields if they are submitted empty
            if (in_array($key, $password_keys) && empty($value)) {
                continue;
            }
            $sanitized_value = sanitize_input($value);
            $stmt->bind_param("ss", $key, $sanitized_value);
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }
        $stmt->close();
        
        if ($success) {
            $conn->commit();
            log_activity($conn, 'UPDATE_SETTINGS', 'Site settings were updated.');
            send_json_response(['success' => true, 'message' => 'Settings saved successfully.']);
        } else {
            $conn->rollback();
            send_json_response(['success' => false, 'message' => 'Failed to save one or more settings.'], 500);
        }
    } catch (Exception $e) {
        $conn->rollback();
        send_json_response(['success' => false, 'message' => 'A database error occurred.'], 500);
    }
}

$conn->close();

