<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// --- Robust Data Reception ---
// This logic intelligently handles both JSON requests (from the main shop page)
// and standard form data requests (from the homepage tabs).
$data = [];
if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

// --- Filter & Pagination Setup ---
// We use trim() here but avoid aggressive sanitization for values going into the DB query.
// Prepared statements will handle the security against SQL injection.
$search_term = trim($data['searchTerm'] ?? '');
$category = trim($data['category'] ?? 'All');
$max_price = (float)($data['maxPrice'] ?? 50000);
$sort_by = trim($data['sortBy'] ?? 'default');
$page = (int)($data['page'] ?? 1);
$limit = (int)($data['limit'] ?? 8); 
$offset = ($page - 1) * $limit;

// --- Dynamic SQL Query Building ---
// We build the query in pieces for security and clarity.
$base_sql = "FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$where_conditions = [];
$params = [];
$types = "";

// Add search term condition if provided
if (!empty($search_term)) {
    $where_conditions[] = "LOWER(p.name) LIKE LOWER(?)";
    $params[] = "%" . $search_term . "%";
    $types .= "s";
}

// Add category condition only if a specific category is selected
// This is now case-insensitive to prevent data mismatch errors.
if ($category !== 'All' && $category !== 'Featured') {
    $where_conditions[] = "LOWER(c.name) = LOWER(?)";
    $params[] = $category;
    $types .= "s";
}

// Add price condition if the slider has been moved
if ($max_price > 0 && $max_price < 50000) {
    $where_conditions[] = "p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

// Combine all conditions into a single WHERE clause
$where_clause = "";
if (!empty($where_conditions)) {
    $where_clause = " AND " . implode(" AND ", $where_conditions);
}

// --- Get Total Product Count for Pagination ---
$count_sql = "SELECT COUNT(p.id) as total " . $base_sql . $where_clause;
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_products = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);
$count_stmt->close();

// --- Sorting Logic ---
// The "Featured" tab on the homepage will use the "latest" sorting method.
if ($category === 'Featured') {
    $sort_by = 'latest';
}
switch ($sort_by) {
    case 'price_asc': $order_by_clause = " ORDER BY p.price ASC"; break;
    case 'price_desc': $order_by_clause = " ORDER BY p.price DESC"; break;
    case 'name_asc': $order_by_clause = " ORDER BY p.name ASC"; break;
    case 'name_desc': $order_by_clause = " ORDER BY p.name DESC"; break;
    case 'latest':
    default:
        $order_by_clause = " ORDER BY p.created_at DESC";
        break;
}

// --- Fetch Final Product List ---
// Combine all pieces into the final query with pagination.
$final_sql = "SELECT p.*, c.name as category_name " . $base_sql . $where_clause . $order_by_clause . " LIMIT ? OFFSET ?";
$final_params = $params;
$final_types = $types . "ii";
$final_params[] = $limit;
$final_params[] = $offset;

$stmt = $conn->prepare($final_sql);
if (!empty($final_params)) {
    // Using a more compatible method for binding a dynamic number of parameters
    $stmt->bind_param($final_types, ...$final_params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- Send JSON Response ---
send_json_response([
    'success' => true,
    'products' => $products,
    'result_count' => count($products),
    'pagination' => [
        'total_products' => $total_products,
        'total_pages' => (int)$total_pages,
        'page' => $page
    ]
]);

$conn->close();

