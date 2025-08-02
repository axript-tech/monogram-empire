<?php
// Monogram Empire - Admin Activity Log API

require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php'; // Ensure only admins can access

$method = $_SERVER['REQUEST_METHOD'];

// --- GET Request: Fetch all activity logs ---
if ($method === 'GET') {
    // Basic pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 15; // Show more logs per page
    $offset = ($page - 1) * $limit;

    // Get total number of logs for pagination
    $total_result = $conn->query("SELECT COUNT(id) as total FROM activity_log");
    $total_logs = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_logs / $limit);

    // Fetch logs for the current page, joining with users to get admin email
    $stmt = $conn->prepare("
        SELECT 
            al.id, 
            al.action,
            al.details,
            al.ip_address,
            al.created_at,
            u.email as admin_email
        FROM activity_log al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }

    $stmt->close();
    $conn->close();

    send_json_response([
        'success' => true,
        'logs' => $logs,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_logs' => $total_logs
        ]
    ], 200);
}

// If the request method is not handled
send_json_response(['error' => 'Invalid request method.'], 405);
?>
