<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

if (!is_admin()) {
    //send_json_response(['success' => false, 'message' => 'Unauthorized'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];

// --- Handle GET requests (Fetch Products) ---
if ($method === 'GET') {
    if (isset($_GET['list']) && $_GET['list'] === 'all') {
        // Fetch a simple list of all products for dropdowns
        $query = "SELECT id, name, sku FROM products ORDER BY name ASC";
        $result = $conn->query($query);
        $products = $result->fetch_all(MYSQLI_ASSOC);
        send_json_response(['success' => true, 'products' => $products]);

    } elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
        // Fetch a single product
        $product_id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        if ($product) {
            send_json_response(['success' => true, 'product' => $product]);
        } else {
            send_json_response(['success' => false, 'message' => 'Product not found.'], 404);
        }
    } else {
        // Fetch all products with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_result = $conn->query("SELECT COUNT(*) as total FROM products");
        $total_products = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_products / $limit);

        $query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        send_json_response([
            'success' => true,
            'products' => $products,
            'pagination' => ['current_page' => $page, 'total_pages' => $total_pages]
        ]);
    }
}

// --- Handle POST requests (Create/Update Product) ---
if ($method === 'POST') {
    $product_id = isset($_POST['id']) && !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $name = sanitize_input($_POST['name'] ?? '');
    $sku = sanitize_input($_POST['sku'] ?? ''); 
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $description = sanitize_input($_POST['description'] ?? '');

    if (empty($name) || $category_id === 0 || $price <= 0) {
        send_json_response(['success' => false, 'message' => 'Name, category, and price are required.'], 400);
    }
    
    $is_update = $product_id !== null;
    $current_paths = [];

    if ($is_update) {
        $stmt = $conn->prepare("SELECT image_url, image_url_2, image_url_3, image_url_4, image_url_5, digital_file_url FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $current_paths = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
    
    function handle_upload($file_key, $upload_dir, &$error_message) {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $error_message = "Failed to create upload directory.";
                    return null;
                }
            }
            $filename = uniqid() . '-' . basename($_FILES[$file_key]['name']);
            $target_file = $upload_dir . $filename;
            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
                return str_replace('../../', '', $upload_dir) . $filename;
            } else {
                $error_message = "Failed to move uploaded file '{$file_key}'.";
                return null;
            }
        } elseif (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] !== UPLOAD_ERR_NO_FILE) {
            $error_message = "Upload error for '{$file_key}': " . $_FILES[$file_key]['error'];
            return null;
        }
        return 'no_file';
    }

    $upload_error = '';
    $uploads_dir = '../../uploads/products/';
    $downloads_dir = '../../uploads/downloads/';

    $image_paths = [];
    for ($i = 1; $i <= 5; $i++) {
        $key = 'image_url_' . $i;
        $result = handle_upload($key, $uploads_dir, $upload_error);
        if ($upload_error) {
            send_json_response(['success' => false, 'message' => $upload_error], 500);
        }
        $image_paths[$i-1] = ($result !== 'no_file') ? $result : ($current_paths[$key] ?? null);
    }

    $digital_file_result = handle_upload('digital_file', $downloads_dir, $upload_error);
    if ($upload_error) {
        send_json_response(['success' => false, 'message' => $upload_error], 500);
    }
    $digital_file_path = ($digital_file_result !== 'no_file') ? $digital_file_result : ($current_paths['digital_file_url'] ?? null);

    if (!$is_update && !$image_paths[0]) {
         send_json_response(['success' => false, 'message' => 'A main image is required for new products.'], 400);
    }
    if (!$is_update && !$digital_file_path) {
         send_json_response(['success' => false, 'message' => 'A downloadable file is required for new products.'], 400);
    }

    if ($is_update) {
        $stmt = $conn->prepare("UPDATE products SET name=?, sku=?, description=?, price=?, category_id=?, image_url=?, image_url_2=?, image_url_3=?, image_url_4=?, image_url_5=?, digital_file_url=? WHERE id=?");
        $stmt->bind_param("sssdissssssi", $name, $sku, $description, $price, $category_id, $image_paths[0], $image_paths[1], $image_paths[2], $image_paths[3], $image_paths[4], $digital_file_path, $product_id);
        if ($stmt->execute()) {
            log_activity($conn, 'UPDATE_PRODUCT', "Updated product ID: $product_id");
            send_json_response(['success' => true, 'message' => 'Product updated successfully.']);
        } else {
            send_json_response(['success' => false, 'message' => 'Database error during update.'], 500);
        }
    } else {
        if (empty($sku)) {
            $cat_stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
            $cat_stmt->bind_param("i", $category_id);
            $cat_stmt->execute();
            $cat_result = $cat_stmt->get_result();
            if ($cat_row = $cat_result->fetch_assoc()) {
                $cat_prefix = strtoupper(substr($cat_row['name'], 0, 3));
                $sku = $cat_prefix . '-' . rand(1000, 9999);
            } else {
                $sku = 'GEN-' . rand(1000, 9999);
            }
            $cat_stmt->close();
        }

        $stmt = $conn->prepare("INSERT INTO products (name, sku, description, price, category_id, image_url, image_url_2, image_url_3, image_url_4, image_url_5, digital_file_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdissssss", $name, $sku, $description, $price, $category_id, $image_paths[0], $image_paths[1], $image_paths[2], $image_paths[3], $image_paths[4], $digital_file_path);
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            log_activity($conn, 'CREATE_PRODUCT', "Created new product ID: $new_id with SKU: $sku");
            send_json_response(['success' => true, 'message' => 'Product created successfully.'], 201);
        } else {
            send_json_response(['success' => false, 'message' => 'Database error during creation.'], 500);
        }
    }
    $stmt->close();
}

// --- Handle DELETE requests ---
if ($method === 'DELETE') {
    $product_id = (int)($_GET['id'] ?? 0);
    if ($product_id === 0) {
        send_json_response(['success' => false, 'message' => 'Invalid product ID.'], 400);
    }

    $stmt = $conn->prepare("SELECT image_url, image_url_2, image_url_3, image_url_4, image_url_5, digital_file_url FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $paths = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        if ($paths) {
            foreach ($paths as $path) {
                if ($path && file_exists('../../' . $path)) {
                    unlink('../../' . $path);
                }
            }
        }
        log_activity($conn, 'DELETE_PRODUCT', "Deleted product ID: $product_id");
        send_json_response(['success' => true, 'message' => 'Product deleted successfully.']);
    } else {
        send_json_response(['success' => false, 'message' => 'Failed to delete product.'], 500);
    }
    $stmt->close();
}

$conn->close();

