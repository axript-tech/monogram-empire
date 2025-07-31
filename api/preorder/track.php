<?php
// Monogram Empire - Track Custom Service Request API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- User Authentication Check ---
// A user must be logged in to track their own orders.
if (!is_logged_in()) {
    send_json_response(['success' => false, 'message' => 'You must be logged in to track an order.'], 401);
}

$user_id = $_SESSION['user_id'];

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validation ---
if (empty($data['tracking_id'])) {
    send_json_response(['success' => false, 'message' => 'Tracking ID is required.'], 400);
}

$tracking_id = sanitize_input($data['tracking_id']);

// --- Fetch Service Request Status ---
// We fetch the request ensuring it belongs to the currently logged-in user.
$stmt = $conn->prepare("
    SELECT 
        tracking_id,
        status,
        details,
        quote_price,
        created_at,
        updated_at
    FROM service_requests 
    WHERE tracking_id = ? AND user_id = ?
");
$stmt->bind_param("si", $tracking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    send_json_response(['success' => false, 'message' => 'No custom order found with that tracking ID.'], 404);
}

$service_request = $result->fetch_assoc();

$stmt->close();
$conn->close();

send_json_response([
    'success' => true,
    'data' => $service_request
], 200);

?>
