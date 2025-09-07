<?php
// Monogram Empire - Secure File Downloader

require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// A user must be logged in to download files.
if (!is_logged_in()) {
    // Redirect to login if not authenticated
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($product_id <= 0 || $order_id <= 0) {
    // Redirect back with an error if parameters are invalid
    header("Location: order-details.php?id=" . $order_id . "&error=invalid_request");
    exit();
}

// --- Security Check: Verify the user has purchased this product in this order ---
$stmt = $conn->prepare("
    SELECT oi.id 
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.product_id = ? 
    AND oi.order_id = ? 
    AND o.user_id = ?
    AND o.status IN ('paid', 'completed')
");
$stmt->bind_param("iii", $product_id, $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    // Redirect back with an error if the user does not have permission
    header("Location: order-details.php?id=" . $order_id . "&error=access_denied");
    exit();
}
$stmt->close();

// --- Fetch the file path from the database ---
$stmt = $conn->prepare("SELECT name, digital_file_url FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($product = $result->fetch_assoc()) {
    $file_path = $product['digital_file_url'];
    $full_server_path = __DIR__ . '/' . $file_path; // Assumes /uploads/ is at the root

    if ($file_path && file_exists($full_server_path)) {
        // Set headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($product['name']) . '.zip"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($full_server_path));
        
        flush(); 
        readfile($full_server_path);
        exit;
    } else {
        // Redirect back with an error if the file is missing on the server
        header("Location: order-details.php?id=" . $order_id . "&error=file_not_found");
        exit();
    }
} else {
    // Redirect back with an error if the product itself is missing
    header("Location: order-details.php?id=" . $order_id . "&error=product_not_found");
    exit();
}

$stmt->close();
$conn->close();
?>
