<?php
// Monogram Empire - Database Connection
// This file establishes the connection to the MySQL database.

// --- Configuration ---
// Replace these with your actual database credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'monogram_empire_db');

// --- Create Connection ---
// We will use the MySQLi extension (MySQL Improved).
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- Check Connection ---
// It's crucial to check if the connection was successful.
// If not, we stop the script and display an error.
if ($conn->connect_error) {
    // In a production environment, you would log this error instead of displaying it.
    die("Connection Failed: " . $conn->connect_error);
}

// --- Set Character Set ---
// This ensures that data is stored and retrieved correctly, especially with different languages.
$conn->set_charset("utf8mb4");

// The $conn variable is now ready to be used in other PHP files to perform database queries.
// Example: include 'includes/db_connect.php';
//          $result = $conn->query("SELECT * FROM users");
?>
