<?php
// Monogram Empire - Shop Product Filtering API

// Include necessary files
require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';

// This API endpoint should only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['error' => 'Invalid request method.'], 405);
}

// Get the raw POST data
$filters = json_decode(file_get_contents('php://input'), true);

// --- Pagination & Filter Variables ---
$page = isset($filters['page']) ? (int)$filters['page'] : 1;
$limit = 9; // 9 products per page (for a 3x3 grid)
$offset = ($page - 1) * $limit;

$base_query = "FROM products p";
$count_query = "SELECT COUNT(p.id) as total ";
$select_query = "SELECT p.id, p.name, p.price, p.image_url ";

$where_clauses = [];
$params = [];
$types = "";

// Join with categories table if needed for category filtering
if (!empty($filters['category']) && $filters['category'] !== 'All') {
    $base_query .= " JOIN categories c ON p.category_id = c.id";
}

// 1. Handle Search Term Filter (if you add a search bar)
if (!empty($filters['searchTerm'])) {
    $where_clauses[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $searchTerm = '%' . sanitize_input($filters['searchTerm']) . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// 2. Handle Category Filter
if (!empty($filters['category']) && $filters['category'] !== 'All') {
    $where_clauses[] = "c.name = ?";
    $params[] = sanitize_input($filters['category']);
    $types .= "s";
}

// 3. Handle Price Range Filter
if (!empty($filters['maxPrice'])) {
    $where_clauses[] = "p.price <= ?";
    $params[] = (float)$filters['maxPrice'];
    $types .= "d";
}

// Construct the WHERE part of the query
if (!empty($where_clauses)) {
    $base_query .= " WHERE " . implode(" AND ", $where_clauses);
}

// --- Get Total Count for Pagination ---
$total_stmt = $conn->prepare($count_query . $base_query);
if (!empty($params)) {
    $total_stmt->bind_param($types, ...$params);
}
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();
$total_products = $total_result['total'];
$total_pages = ceil($total_products / $limit);
$total_stmt->close();

// --- Get Products for the Current Page ---
// 4. Handle Sorting
$sort_options = [
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'latest' => 'p.created_at DESC',
    'default' => 'p.id ASC'
];
$sort_by = 'default';
if (!empty($filters['sortBy']) && array_key_exists($filters['sortBy'], $sort_options)) {
    $sort_by = $filters['sortBy'];
}
$base_query .= " ORDER BY " . $sort_options[$sort_by];

// 5. Add Limit and Offset for Pagination
$base_query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// Execute the final query
$stmt = $conn->prepare($select_query . $base_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$conn->close();

send_json_response([
    'success' => true,
    'products' => $products,
    'pagination' => [
        'page' => $page,
        'total_pages' => $total_pages,
        'total_products' => $total_products
    ]
], 200);
?>
