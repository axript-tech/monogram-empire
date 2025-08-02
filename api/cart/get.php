<?php
// Monogram Empire - Get Cart Contents API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept GET requests.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to view your cart.'], 401);
}

$user_id = $_SESSION['user_id'];

// --- Fetch Cart Items ---
// We join the cart table with the products table to get product details.
$stmt = $conn->prepare("
    SELECT 
        c.id as cart_item_id,
        p.id as product_id,
        p.name,
        p.price,
        p.image_url
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$subtotal = 0.00;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal += (float)$row['price'];
}

$stmt->close();
$conn->close();

// FIX: Send the subtotal as a raw number for accurate frontend calculations.
// The frontend will handle the display formatting.
send_json_response([
    'success' => true,
    'cart_items' => $cart_items,
    'item_count' => count($cart_items),
    'subtotal' => $subtotal 
], 200);

?>
