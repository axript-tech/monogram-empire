<?php 
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

// Fetch categories for the dynamic product section
$categories = [];
$category_query = "SELECT name FROM categories ORDER BY name ASC";
$category_result = $conn->query($category_query);
if ($category_result && $category_result->num_rows > 0) {
    while($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch 4 "Top" designs (e.g., the most recent ones for this example)
$top_designs = [];
$top_designs_query = "SELECT id, name, price, image_url, sku FROM products ORDER BY created_at DESC LIMIT 4";
$top_designs_result = $conn->query($top_designs_query);
if($top_designs_result) {
    $top_designs = $top_designs_result->fetch_all(MYSQLI_ASSOC);
}


include 'includes/header.php'; 
?>

<!-- Hero Section with Full Screen Video -->
<section class="relative h-screen flex items-center justify-center text-white text-center">
    <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0">
        <source src="https://videos.pexels.com/video-files/4784090/4784090-hd_1920_1080_25fps.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="absolute inset-0 bg-black bg-opacity-60 z-10"></div>
    <div class="relative z-20 p-6">
        <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
            Crafting Legacy, One Thread at a Time
        </h1>
        <p class="text-lg md:text-xl text-gray-200 max-w-3xl mx-auto mb-8">
            Monogram Empire is where timeless artistry meets modern identity. Discover designs that tell your unique story.
        </p>
        <a href="shop.php" class="bg-brand-gold text-brand-dark font-bold py-4 px-10 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
            Discover the Collection
        </a>
    </div>
</section>

<!-- Top Designs Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-2 text-brand-dark">Discover Our Top Designs</h2>
        <div class="w-20 h-1 bg-brand-gold mx-auto mb-12"></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach($top_designs as $product): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden group transition-transform transform hover:-translate-y-2">
                <div class="relative">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-72 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="product-details.php?id=<?= $product['id'] ?>" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-brand-dark truncate"><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="text-brand-gray text-2xl font-bold mt-1">&#8358;<?= number_format($product['price']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Explore Our Collection (Sortable) -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-2 text-brand-dark">Explore Our Collection</h2>
        <div class="w-20 h-1 bg-brand-gold mx-auto mb-10"></div>

        <!-- Category Filter Tabs -->
        <div class="flex justify-center flex-wrap gap-2 md:gap-4 mb-8">
            <button class="collection-tab bg-brand-dark text-white font-semibold py-2 px-5 rounded-full" data-category="All">All Designs</button>
            <?php foreach ($categories as $category): ?>
                <button class="collection-tab bg-white text-brand-dark font-semibold py-2 px-5 rounded-full shadow-sm" data-category="<?= htmlspecialchars($category['name']) ?>"><?= htmlspecialchars($category['name']) ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Product Grid -->
        <div id="collection-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <!-- Products will be loaded here by JavaScript -->
        </div>
    </div>
</section>

<!-- Our Philosophy Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-brand-dark mb-4">Our Design Philosophy</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-12">We believe every monogram is more than just letters—it's a mark of identity, a statement of quality, and a piece of art.</p>
        <div class="grid md:grid-cols-3 gap-12">
            <div class="p-4">
                <div class="text-5xl text-brand-gold mb-4"><i class="fas fa-drafting-compass"></i></div>
                <h3 class="text-xl font-bold text-brand-dark mb-2">Precision</h3>
                <p class="text-gray-600">Every curve and line is meticulously crafted for perfect balance and clarity.</p>
            </div>
            <div class="p-4">
                <div class="text-5xl text-brand-gold mb-4"><i class="fas fa-feather-alt"></i></div>
                <h3 class="text-xl font-bold text-brand-dark mb-2">Elegance</h3>
                <p class="text-gray-600">We blend classic principles with modern aesthetics to create timeless designs.</p>
            </div>
            <div class="p-4">
                <div class="text-5xl text-brand-gold mb-4"><i class="fas fa-handshake"></i></div>
                <h3 class="text-xl font-bold text-brand-dark mb-2">Collaboration</h3>
                <p class="text-gray-600">For our bespoke services, we partner with you to bring your unique vision to life.</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="bg-brand-gold">
    <div class="container mx-auto px-6 py-16 text-center">
        <h2 class="text-3xl font-bold text-brand-dark">Ready to Make Your Mark?</h2>
        <p class="text-gray-800 mt-2 max-w-2xl mx-auto">From personal stationery to professional branding, our designs provide the perfect touch of distinction. Find yours today.</p>
        <a href="shop.php" class="mt-8 inline-block bg-brand-dark text-white font-bold py-4 px-10 rounded-lg hover:bg-gray-700 transition-colors text-lg">
            Shop The Full Collection
        </a>
    </div>
</section>


<?php 
$conn->close();
include 'includes/footer.php'; 
?>
