<?php
// ------------------------------------------------------
// PEEF Platform - Members API Endpoint
// ------------------------------------------------------
// This script provides a secure API to fetch member data
// from the database and returns it in JSON format.
// Access is restricted to authenticated administrators.
// ------------------------------------------------------

// Set the content type header to JSON.
header('Content-Type: application/json');

// Include all necessary core files.
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php'; // This also starts the session.

// ** Security Check **
if (!is_admin_logged_in()) {
    http_response_code(403); // Forbidden
    echo json_encode(['data' => []]); // Return empty data for security
    exit;
}

// Initialize the response array for DataTables.
$response = [
    'data' => []
];

try {
    // Fetch all members (users with the 'Member' role)
    $sql = "SELECT 
                u.id, 
                u.full_name, 
                u.email, 
                u.phone_number, 
                mt.name as membership_tier,
                u.membership_end_date,
                u.is_active
            FROM users u
            LEFT JOIN membership_tiers mt ON u.membership_tier_id = mt.id
            WHERE u.role = 'Member' 
            ORDER BY u.full_name ASC";
    
    $stmt = $pdo->query($sql);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['data'] = $members;

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    $response = [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => []
    ];
    // In production, you would log the error instead of displaying the message.
    // error_log($e->getMessage());
}

// Encode the response array into JSON and output it.
echo json_encode($response);
?>
