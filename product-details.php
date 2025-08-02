<?php 
include 'includes/functions.php';
include 'includes/header.php'; 
include 'includes/db_connect.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    echo "<p class='text-center py-20'>Invalid product ID.</p>";
    include 'includes/footer.php';
    exit();
}

// Fetch the product details including all images and category name
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-center py-20'>Product not found.</p>";
    include 'includes/footer.php';
    exit();
}
$product = $result->fetch_assoc();
$stmt->close();

// Create an array of available images
$product_images = array_filter([
    $product['image_url'],
    $product['image_url_2'],
    $product['image_url_3'],
    $product['image_url_4'],
    $product['image_url_5'],
]);

// Fetch related products
$related_stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
$conn->close();
?>

<!-- Product Details Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12 items-start">

            <!-- Product Image Gallery -->
            <div>
                <div class="mb-4">
                    <img id="main-product-image" src=".<?php echo htmlspecialchars($product['image_url']); ?>" alt="Main product image of <?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto rounded-lg shadow-lg">
                </div>
                <?php if (count($product_images) > 1): ?>
                <div class="grid grid-cols-5 gap-4">
                    <?php foreach ($product_images as $index => $img_url): ?>
                    <img src=".<?php echo htmlspecialchars($img_url); ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="thumbnail-image cursor-pointer rounded-md border-2 <?php echo $index === 0 ? 'border-brand-gold' : 'border-transparent'; ?> hover:border-brand-gold">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Information -->
            <div>
                <div class="mb-4">
                    <a href="shop.php?category=<?php echo urlencode($product['category_name']); ?>" class="text-sm font-semibold text-brand-gold uppercase tracking-wider hover:underline"><?php echo htmlspecialchars($product['category_name']); ?></a>
                    <h1 class="text-4xl font-bold text-brand-dark mt-1" style="font-family: 'Playfair Display', serif;"><?php echo htmlspecialchars($product['name']); ?></h1>
                </div>
                <p class="text-2xl text-brand-gray font-semibold mb-6">&#8358;<?php echo number_format($product['price'], 2); ?></p>
                
                <div class="prose max-w-none text-gray-600 leading-relaxed mb-6">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>

                <!-- Container for AJAX messages -->
                <div id="add-to-cart-message-container" class="mb-4" style="display: none;"></div>

                <!-- Add to Cart Form -->
                <form id="add-to-cart-form" class="mb-8">
                    <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </form>

                <!-- SKU and Social Share -->
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-500 mb-4"><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-semibold text-gray-700">Share:</span>
                        <a href="#" class="text-gray-500 hover:text-brand-gold"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-500 hover:text-brand-gold"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-500 hover:text-brand-gold"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="py-16 bg-brand-light-gray">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-10">You Might Also Like</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php while($related_product = $related_result->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                <div class="relative">
                    <img src=".<?php echo htmlspecialchars($related_product['image_url']); ?>" alt="<?php echo htmlspecialchars($related_product['name']); ?>" class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="product-details.php?id=<?php echo $related_product['id']; ?>" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-brand-dark"><?php echo htmlspecialchars($related_product['name']); ?></h3>
                    <p class="text-brand-gray">&#8358;<?php echo number_format($related_product['price'], 2); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- IMPORTANT: Include the page-specific JavaScript file AFTER the footer -->
