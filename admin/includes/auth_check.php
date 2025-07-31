<?php
// Monogram Empire - Admin Authentication Check

// Start the session if it's not already started.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin_id session variable is set.
// If it's not set, the user is not logged in as an admin.
if (!isset($_SESSION['admin_id'])) {
    // Redirect them to the admin login page.
    // The path needs to be absolute from the web root or relative from this file's location.
    header("Location: auth/login.php");
    // Stop script execution to prevent the rest of the page from loading.
    exit();
}

// If the script reaches this point, the user is an authenticated admin.
?>
