<?php
// This must be at the very top of the file before any output.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitizes user input to prevent XSS.
 * @param string $data The input data.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Sends a standardized JSON response and exits the script.
 * @param array $response_data The associative array to be sent as JSON.
 * @param int $http_code The HTTP status code to send.
 */
function send_json_response($response_data, $http_code = 200) {
    header('Content-Type: application/json');
    http_response_code($http_code);
    echo json_encode($response_data);
    exit();
}

/**
 * Checks if a user is currently logged in.
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if the logged-in user is an administrator.
 * @return bool True if user is an admin, false otherwise.
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Logs an administrator's action to the activity log table.
 * @param mysqli $conn The database connection object.
 * @param string $action The action performed (e.g., 'CREATE_USER', 'UPDATE_PRODUCT').
 * @param string $details A description of the action.
 */
function log_activity($conn, $action, $details) {
    if (!is_admin()) return; // Only log actions for admins

    $admin_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isss", $admin_id, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * Redirects the user to a specified URL.
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

