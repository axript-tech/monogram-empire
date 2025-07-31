<?php
// Monogram Empire - Admin Logout

// Always start the session to access session variables.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Unset all of the session variables.
$_SESSION = array();

// 2. If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finally, destroy the session.
session_destroy();

// 4. Redirect to the admin login page.
header("Location: login.php");
exit();
?>
