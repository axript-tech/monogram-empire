<?php 
include 'includes/header.php'; 
include 'includes/db_connect.php';

// Get the product ID from the URL, ensuring it's an integer
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    // If no valid ID is provided, show an error or redirect
    echo "<p class='text-center py-20'>Invalid product ID.</p>";
    include 'includes/footer.php';
    exit();
}

// Fetch the product details from the database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no product is found with that ID
    echo "<p class='text-center py-20'>Product not found.</p>";
    include 'includes/footer.php';
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Fetch related products from the same category
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
                    <img id="main-product-image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Main product image of <?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto rounded-lg shadow-lg">
                </div>
                <!-- Thumbnails can be added here if you have multiple images per product in your DB -->
            </div>

            <!-- Product Information -->
            <div>
                <h1 class="text-4xl font-bold text-brand-dark mb-2" style="font-family: 'Playfair Display', serif;"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="text-2xl text-brand-gray font-semibold mb-6">&#8358;<?php echo number_format($product['price'], 2); ?></p>
                
                <div class="prose max-w-none text-gray-600 leading-relaxed mb-6">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>

                <!-- Add to Cart Form -->
                <form id="add-to-cart-form" class="mb-8">
                    <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                        <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                    </button>
                </form>

                <!-- Accordion for Extra Details -->
                <div class="space-y-2" id="details-accordion">
                    <div class="border rounded-lg">
                        <div class="details-question cursor-pointer flex justify-between items-center p-4">
                            <h3 class="font-semibold text-brand-dark">What's Included?</h3>
                            <i class="fas fa-chevron-down text-brand-gold transition-transform"></i>
                        </div>
                        <div class="details-answer hidden p-4 pt-0 text-gray-600">
                            <ul class="list-disc list-inside space-y-1">
                                <li>SVG file (Vector)</li>
                                <li>PNG file (Transparent Background)</li>
                                <li>PDF file (Print Ready)</li>
                                <li>JPEG file</li>
                            </ul>
                        </div>
                    </div>
                    <div class="border rounded-lg">
                        <div class="details-question cursor-pointer flex justify-between items-center p-4">
                            <h3 class="font-semibold text-brand-dark">License Information</h3>
                            <i class="fas fa-chevron-down text-brand-gold transition-transform"></i>
                        </div>
                        <div class="details-answer hidden p-4 pt-0 text-gray-600">
                            <p>Our standard license allows for unlimited personal use and small commercial use (up to 500 units). For extended licensing, please contact us.</p>
                        </div>
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
                    <img src="<?php echo htmlspecialchars($related_product['image_url']); ?>" alt="<?php echo htmlspecialchars($related_product['name']); ?>" class="w-full h-64 object-cover">
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
<!-- Page-specific JS should be included after the main footer scripts if needed -->
