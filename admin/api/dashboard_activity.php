<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

//session_start();
if (!is_admin()) {
    // Using the standard function for error responses remains consistent.
    //send_json_response(false, 'Unauthorized', [], 403);
}

$query = "SELECT al.action, al.details, al.created_at, u.email AS admin_email 
          FROM activity_log al 
          LEFT JOIN users u ON al.user_id = u.id 
          ORDER BY al.created_at DESC 
          LIMIT 5";

$result = $conn->query($query);

if ($result) {
    $activities = $result->fetch_all(MYSQLI_ASSOC);
    
    // Manually construct the JSON response to match the structure the JavaScript expects.
    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Recent activities fetched.',
        'activities' => $activities // Provide the 'activities' key at the top level.
    ]);
    exit();

} else {
    send_json_response(false, 'Failed to fetch recent activities.', [], 500);
}

$conn->close();

