<?php
// This MUST be the very first line to avoid "headers already sent" errors.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

// 1. Get and validate the Product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id === 0) {
    redirect('shop.php'); // Redirect if no valid ID is provided
}

// 2. Fetch the main product details from the database
$stmt = $conn->prepare(
    "SELECT p.*, c.name AS category_name 
     FROM products p 
     JOIN categories c ON p.category_id = c.id 
     WHERE p.id = ?"
);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// 3. If the product doesn't exist, redirect back to the shop
if (!$product) {
    redirect('shop.php');
}

// 4. Fetch related products from the same category
$related_products = [];
$related_stmt = $conn->prepare(
    "SELECT id, name, price, image_url 
     FROM products 
     WHERE category_id = ? AND id != ? 
     ORDER BY RAND() 
     LIMIT 4"
);
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
if ($related_result) {
    $related_products = $related_result->fetch_all(MYSQLI_ASSOC);
}
$related_stmt->close();

include 'includes/header.php';
?>

<!-- Main Content -->
<div class="bg-white">
    <div class="container mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Image Gallery -->
            <div>
                <div class="mb-4 rounded-lg overflow-hidden shadow-lg">
                    <img id="main-product-image" src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-auto object-cover transition-transform duration-300 transform hover:scale-105">
                </div>
                <div class="flex space-x-2">
                    <?php
                    // Create an array of all available images
                    $images = array_filter([
                        $product['image_url'], 
                        $product['image_url_2'], 
                        $product['image_url_3'], 
                        $product['image_url_4'], 
                        $product['image_url_5']
                    ]);
                    foreach ($images as $img_url): ?>
                        <img src="<?= htmlspecialchars($img_url) ?>" alt="Thumbnail" class="thumbnail-image w-20 h-20 object-cover rounded-md cursor-pointer border-2 border-transparent hover:border-brand-gold transition-all">
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Product Details -->
            <div>
                <p class="text-sm text-gray-500 mb-2">Category: <a href="shop.php?category=<?= urlencode($product['category_name']) ?>" class="text-brand-gold hover:underline"><?= htmlspecialchars($product['category_name']) ?></a></p>
                <h1 class="text-4xl font-bold text-brand-dark" style="font-family: 'Playfair Display', serif;"><?= htmlspecialchars($product['name']) ?></h1>
                <?php if ($product['sku']): ?>
                    <p class="text-sm text-gray-400 mt-2">SKU: <?= htmlspecialchars($product['sku']) ?></p>
                <?php endif; ?>

                <p class="text-4xl text-brand-dark my-4"><?= '&#8358;' . number_format($product['price'], 2) ?></p>

                <div class="prose max-w-none text-gray-600 mb-6">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </div>

                <!-- Add to Cart Form -->
                <div id="add-to-cart-message-container" class="mb-4"></div>
                <form id="add-to-cart-form">
                    <button type="submit" class="w-full bg-brand-dark text-white font-bold py-4 px-8 rounded-lg hover:bg-gray-700 transition-colors flex items-center justify-center text-lg">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </form>

                <div class="mt-8 border-t pt-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-2">Share This Design</h3>
                    <div class="flex items-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-600"><i class="fab fa-facebook-f fa-2x"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-gray-400 hover:text-red-600"><i class="fab fa-pinterest fa-2x"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<section class="bg-gray-50 py-20">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12 text-brand-dark">You Might Also Like</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($related_products as $related): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                    <div class="relative">
                        <img src="<?= htmlspecialchars($related['image_url']) ?>" alt="<?= htmlspecialchars($related['name']) ?>" class="w-full h-64 object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="product-details.php?id=<?= $related['id'] ?>" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="text-lg font-semibold text-brand-dark"><?= htmlspecialchars($related['name']) ?></h3>
                        <p class="text-brand-gray">&#8358;<?= number_format($related['price'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>
<!-- Page-specific scripts required for functionality -->
<script src="assets/js/cart.js"></script>

