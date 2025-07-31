<?php
// Monogram Empire - Core Functions
// This file contains helper functions used across the application.

// Start the session. This is needed for user authentication, cart, etc.
// It's placed here so that any file including this will have session capabilities.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sends a standardized JSON response back to the client.
 * Sets the appropriate content type header and terminates the script.
 *
 * @param array $data The data to be encoded into JSON.
 * @param int $statusCode The HTTP status code to send (e.g., 200 for OK, 400 for Bad Request).
 */
function send_json_response($data, $statusCode = 200) {
    // Set the HTTP response code.
    http_response_code($statusCode);
    
    // Set the content type header to indicate a JSON response.
    header('Content-Type: application/json');
    
    // Encode the data array into a JSON string and output it.
    echo json_encode($data);
    
    // Terminate the script to prevent further execution.
    exit();
}

/**
 * Sanitizes user input to prevent Cross-Site Scripting (XSS) attacks.
 * This should be used on any data that will be displayed back to the user.
 *
 * @param string $data The raw input data.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    // Trim whitespace from the beginning and end of the string.
    $data = trim($data);
    // Remove backslashes.
    $data = stripslashes($data);
    // Convert special characters to HTML entities.
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * A simple function to check if a user is logged in.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Generates a secure random token for things like password resets.
 *
 * @param int $length The length of the token to generate.
 * @return string The generated token in hexadecimal format.
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

// You can add more helper functions here as the application grows,
// such as functions for logging errors, validating emails, etc.
?>
