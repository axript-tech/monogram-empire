<?php
// Monogram Empire - Remove from Cart API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to modify your cart.'], 401);
}

$user_id = $_SESSION['user_id'];

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
if (empty($data['cart_item_id']) || !is_numeric($data['cart_item_id'])) {
    send_json_response(['success' => false, 'message' => 'Invalid cart item ID.'], 400);
}

$cart_item_id = (int)$data['cart_item_id'];

// --- Remove Item from Cart ---
// We delete the item from the cart table, ensuring it belongs to the currently logged-in user to prevent unauthorized deletions.
$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_item_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // Item was successfully removed.
        send_json_response(['success' => true, 'message' => 'Item removed from cart.'], 200);
    } else {
        // No rows were affected, meaning the item either didn't exist or didn't belong to the user.
        send_json_response(['success' => false, 'message' => 'Item not found in your cart.'], 404);
    }
} else {
    // A database error occurred.
    send_json_response(['success' => false, 'message' => 'Failed to remove item from cart. Please try again.'], 500);
}

$stmt->close();
$conn->close();
?>
