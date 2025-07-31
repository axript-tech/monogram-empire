<?php
// Monogram Empire - Admin Dashboard Stats API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

// This API endpoint should only accept GET requests.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// --- Fetch Dashboard Statistics ---
$stats = [];

// 1. Total Revenue (from successful payments)
$result = $conn->query("SELECT SUM(amount) as total_revenue FROM payments WHERE status = 'successful'");
$stats['total_revenue'] = $result->fetch_assoc()['total_revenue'] ?? 0.00;

// 2. Total Orders (paid or completed)
$result = $conn->query("SELECT COUNT(id) as total_orders FROM orders WHERE status IN ('paid', 'completed')");
$stats['total_orders'] = $result->fetch_assoc()['total_orders'] ?? 0;

// 3. Total Users (customers only)
$result = $conn->query("SELECT COUNT(id) as total_users FROM users WHERE role = 'customer'");
$stats['total_users'] = $result->fetch_assoc()['total_users'] ?? 0;

// 4. Pending Service Requests
$result = $conn->query("SELECT COUNT(id) as pending_requests FROM service_requests WHERE status = 'pending'");
$stats['pending_requests'] = $result->fetch_assoc()['pending_requests'] ?? 0;

$conn->close();

send_json_response([
    'success' => true,
    'stats' => $stats
], 200);

?>
