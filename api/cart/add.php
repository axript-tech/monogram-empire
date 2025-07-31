<?php
// Monogram Empire - Add to Cart API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
// A user must be logged in to add items to their cart.
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to add items to your cart.'], 401); // 401 Unauthorized
}

$user_id = $_SESSION['user_id'];

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
if (empty($data['product_id']) || !is_numeric($data['product_id'])) {
    send_json_response(['success' => false, 'message' => 'Invalid product ID.'], 400);
}

$product_id = (int)$data['product_id'];

// --- Check if Product Exists ---
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    send_json_response(['success' => false, 'message' => 'Product not found.'], 404); // 404 Not Found
}
$stmt->close();

// --- Check if Item is Already in Cart ---
// Since these are single-purchase digital products, we prevent adding the same item twice.
$stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    send_json_response(['success' => false, 'message' => 'This item is already in your cart.'], 409); // 409 Conflict
}
$stmt->close();

// --- Add Item to Cart ---
// The quantity is always 1 for this business logic.
$quantity = 1;

$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $user_id, $product_id, $quantity);

if ($stmt->execute()) {
    // Get the new total number of items in the cart to send back to the frontend.
    $count_stmt = $conn->prepare("SELECT COUNT(id) as total_items FROM cart WHERE user_id = ?");
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result()->fetch_assoc();
    $total_items = $count_result['total_items'];
    $count_stmt->close();

    send_json_response([
        'success' => true, 
        'message' => 'Item added to cart successfully!',
        'total_items' => $total_items
    ], 201); // 201 Created
} else {
    send_json_response(['success' => false, 'message' => 'Failed to add item to cart. Please try again.'], 500);
}

$stmt->close();
$conn->close();
?>
