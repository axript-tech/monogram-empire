<?php 
include 'includes/functions.php';
include 'includes/header.php'; 
include 'includes/db_connect.php';

// Fetch categories for the new tabbed section
$categories = [];
$category_query = "SELECT name FROM categories ORDER BY name ASC";
$category_result = $conn->query($category_query);
if ($category_result && $category_result->num_rows > 0) {
    while($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$conn->close();
?>

<!-- Hero Slider Section -->
<div class="swiper-container hero-slider">
    <div class="swiper-wrapper">
        <!-- Slide 1 -->
        <div class="swiper-slide relative bg-brand-dark text-white">
            <img src="assets/images/bespoke.png" class="absolute inset-0 w-full h-full object-cover opacity-40">
            <div class="relative container mx-auto px-6 h-full flex flex-col justify-center items-center text-center py-24 md:py-32">
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
                    Your Identity, <span class="text-brand-gold">Elegantly Stitched</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 max-w-2xl mb-8">
                    Discover exclusive monogram templates that blend timeless tradition with modern sophistication.
                </p>
                <a href="shop.php" class="bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                    Explore Designs
                </a>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="swiper-slide relative bg-brand-dark text-white">
            <img src="assets/images/bespoke2.png" class="absolute inset-0 w-full h-full object-cover opacity-40">
            <div class="relative container mx-auto px-6 h-full flex flex-col justify-center items-center text-center py-24 md:py-32">
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
                    Truly <span class="text-brand-gold">Bespoke Service</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 max-w-2xl mb-8">
                    Have a unique vision? Our designers will work with you to create a one-of-a-kind monogram.
                </p>
                <a href="request-service.php" class="border-2 border-brand-gold text-white font-bold py-3 px-8 rounded-full text-lg hover:bg-brand-gold hover:text-brand-dark transition-colors">
                    Request a Custom Design
                </a>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="swiper-slide relative bg-brand-dark text-white">
            <img src="assets/images/bespoke5.jpg" class="absolute inset-0 w-full h-full object-cover opacity-40">
            <div class="relative container mx-auto px-6 h-full flex flex-col justify-center items-center text-center py-24 md:py-32">
                <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-4" style="font-family: 'Playfair Display', serif;">
                    Freshly <span class="text-brand-gold">Designed</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 max-w-2xl mb-8">
                    Check out the latest additions to our curated collection of premium templates.
                </p>
                <a href="shop.php?sort=latest" class="bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                    Shop New Arrivals
                </a>
            </div>
        </div>
    </div>
    <!-- Add Pagination -->
    <div class="swiper-pagination"></div>
    <!-- Add Navigation -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>


<!-- Why Choose Us Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 text-center">
        <div class="grid md:grid-cols-3 gap-12">
            <div class="p-4">
                <div class="text-5xl text-brand-gold mb-4"><i class="fas fa-gem"></i></div>
                <h3 class="text-xl font-bold text-brand-dark mb-2">Exquisite Quality</h3>
                <p class="text-gray-600">High-resolution, professionally crafted digital files perfect for any application, from print to embroidery.</p>
            </div>
            <div class="p-4">
                <div class="text-5xl text-brand-gold mb-4"><i class="fas fa-bolt"></i></div>
                <h3 class="text-xl font-bold text-brand-dark mb-2">Instant Download</h3>
                <p class="text-gray-600">Get immediate access to your purchased design files. No waiting, just creating.</p>
            </div>
            <div class="p-4">
                <div class="text-5xl text-brand-gold mb-4"><i class="fas fa-drafting-compass"></i></div>
                <h3 class="text-xl font-bold text-brand-dark mb-2">Bespoke Service</h3>
                <p class="text-gray-600">Need something unique? Our designers are ready to craft a one-of-a-kind monogram just for you.</p>
            </div>
        </div>
    </div>
</section>

<!-- Explore Collection Section (New Tabbed Interface) -->
<section class="py-16 bg-brand-light-gray">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-2 text-brand-dark">Explore Our Collection</h2>
        <div class="w-20 h-1 bg-brand-gold mx-auto mb-10"></div>

        <!-- Tab Buttons -->
        <div class="flex justify-center flex-wrap gap-2 md:gap-4 mb-8">
            <button class="collection-tab bg-brand-dark text-white font-semibold py-2 px-5 rounded-full" data-category="Featured">Featured</button>
            <?php foreach ($categories as $category): ?>
                <button class="collection-tab bg-white text-brand-dark font-semibold py-2 px-5 rounded-full" data-category="<?php echo htmlspecialchars($category['name']); ?>"><?php echo htmlspecialchars($category['name']); ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Product Grid -->
        <div id="collection-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Products will be loaded here by JavaScript -->
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-20 bg-brand-dark text-white">
    <div class="container mx-auto px-6 text-center">
        <i class="fas fa-quote-left text-4xl text-brand-gold mb-4"></i>
        <p class="text-2xl italic text-gray-300 max-w-3xl mx-auto">
            "The attention to detail is simply stunning. Monogram Empire provided the perfect centerpiece for our wedding invitations. Absolutely flawless!"
        </p>
        <p class="mt-6 font-bold text-lg">- A. Adebayo</p>
    </div>
</section>

<!-- Custom Service Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2 rounded-lg shadow-2xl overflow-hidden">
            <video class="w-full h-full object-cover" autoplay loop muted playsinline>
                <source src="https://videos.pexels.com/video-files/853874/853874-hd_1920_1080_25fps.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="md:w-1/2 text-center md:text-left">
            <h2 class="text-3xl font-bold text-brand-dark mb-4">Need Something Unique?</h2>
            <p class="text-gray-600 mb-6">
                Our bespoke design service allows you to collaborate with our designers to create a truly one-of-a-kind monogram. Perfect for weddings, special gifts, or personal branding.
            </p>
            <a href="request-service.php" class="border-2 border-brand-dark text-brand-dark font-bold py-3 px-8 rounded-full hover:bg-brand-dark hover:text-white transition-all">
                Request a Custom Design
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<!-- SwiperJS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
