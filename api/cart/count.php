<?php
// Monogram Empire - Cart Item Count API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept GET requests.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

$item_count = 0;

// If the user is logged in, get their cart count from the database.
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT COUNT(id) as total_items FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $item_count = $result['total_items'] ?? 0;
    
    $stmt->close();
    $conn->close();
}

// Return the count. This will be 0 if the user is not logged in.
send_json_response([
    'success' => true,
    'item_count' => $item_count
], 200);

?>
