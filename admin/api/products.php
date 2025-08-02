<?php
// Monogram Empire - Admin Products API

require_once '../../includes/db_connect.php';
require_once '../../includes/functions.php';
require_once '../includes/auth_check.php';

$method = $_SERVER['REQUEST_METHOD'];

// Helper function for file uploads
function handle_upload($file_key, $upload_dir) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
        if (!is_dir($upload_dir)) {
            // Create the directory recursively if it doesn't exist
            mkdir($upload_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('prod_', true) . '.' . $file_extension;
        $target_path = $upload_dir . $unique_filename;
        
        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_path)) {
            // Return a web-accessible path, removing the '../../' part
            return str_replace('../../', '/', $target_path);
        }
    }
    return null;
}

// --- GET Request: Fetch products ---
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $product_id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT id, name, description, price, category_id, image_url, image_url_2, image_url_3, image_url_4, image_url_5 FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($product = $result->fetch_assoc()) {
            send_json_response(['success' => true, 'product' => $product], 200);
        } else {
            send_json_response(['success' => false, 'message' => 'Product not found.'], 404);
        }
    } else {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $total_result = $conn->query("SELECT COUNT(id) as total FROM products");
        $total_products = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_products / $limit);
        $stmt = $conn->prepare("SELECT p.id, p.name, p.price, p.image_url, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        send_json_response(['success' => true, 'products' => $products, 'pagination' => ['current_page' => $page, 'total_pages' => $total_pages, 'total_products' => $total_products]], 200);
    }
}

// --- POST Request: Create or Update a product ---
if ($method === 'POST') {
    $is_update = !empty($_POST['product_id']);
    
    if (empty($_POST['name']) || empty($_POST['category_id']) || empty($_POST['price']) || empty($_POST['description'])) {
        send_json_response(['success' => false, 'message' => 'Please fill all required fields.'], 400);
    }
    if ($is_update == false && empty($_FILES['image_url'])) {
        send_json_response(['success' => false, 'message' => 'Main product image is required for new products.'], 400);
    }
    if ($is_update == false && empty($_FILES['digital_file_url'])) {
        send_json_response(['success' => false, 'message' => 'Digital file (ZIP) is required for new products.'], 400);
    }

    $name = sanitize_input($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = sanitize_input($_POST['description']);

    $image_paths = [
        'image_url' => handle_upload('image_url', '../../uploads/products/images/'),
        'image_url_2' => handle_upload('image_url_2', '../../uploads/products/images/'),
        'image_url_3' => handle_upload('image_url_3', '../../uploads/products/images/'),
        'image_url_4' => handle_upload('image_url_4', '../../uploads/products/images/'),
        'image_url_5' => handle_upload('image_url_5', '../../uploads/products/images/'),
    ];
    $file_path = handle_upload('digital_file_url', '../../uploads/products/files/');

    if ($is_update) {
        $product_id = (int)$_POST['product_id'];
        $query = "UPDATE products SET name = ?, category_id = ?, price = ?, description = ?";
        $types = "sids";
        $params = [$name, $category_id, $price, $description];
        
        foreach($image_paths as $key => $path) {
            if ($path) { $query .= ", $key = ?"; $types .= "s"; $params[] = $path; }
        }
        if ($file_path) { $query .= ", digital_file_url = ?"; $types .= "s"; $params[] = $file_path; }
        
        $query .= " WHERE id = ?";
        $types .= "i";
        $params[] = $product_id;
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $message = 'Product updated successfully!';
    } else {
        $sku = 'ME-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, description, sku, image_url, image_url_2, image_url_3, image_url_4, image_url_5, digital_file_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidssssssss", $name, $category_id, $price, $description, $sku, $image_paths['image_url'], $image_paths['image_url_2'], $image_paths['image_url_3'], $image_paths['image_url_4'], $image_paths['image_url_5'], $file_path);
        $message = 'Product created successfully!';
    }

    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => $message], $is_update ? 200 : 201);
    } else {
        send_json_response(['success' => false, 'message' => 'Database operation failed.'], 500);
    }
}

// --- DELETE Request ---
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['product_id'])) {
        send_json_response(['success' => false, 'message' => 'Product ID is required.'], 400);
    }
    $product_id = (int)$data['product_id'];

    $stmt = $conn->prepare("SELECT image_url, image_url_2, image_url_3, image_url_4, image_url_5, digital_file_url FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($product = $result->fetch_assoc()) {
        // Delete all associated files from server
        foreach($product as $file_path) {
            if ($file_path && file_exists('../..' . $file_path)) {
                unlink('../..' . $file_path);
            }
        }
    }
    $stmt->close();
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        send_json_response(['success' => true, 'message' => 'Product deleted successfully.'], 200);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete product.'], 500);
    }
}

$conn->close();
send_json_response(['error' => 'Invalid request method.'], 405);
?>
