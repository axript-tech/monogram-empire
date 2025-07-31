<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="relative bg-brand-dark text-white" style="height: 60vh;">
    <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 h-full flex flex-col justify-center items-center text-center">
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

<!-- Featured Products Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-2 text-brand-dark">Featured Designs</h2>
        <div class="w-20 h-1 bg-brand-gold mx-auto mb-10"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Product Card 1 (Placeholder) -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                <div class="relative">
                    <img src="https://placehold.co/400x400/f2f2f2/1a1a1a?text=Design+1" alt="Monogram Design 1" class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="product-details.php?id=1" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-brand-dark">Victorian Crest</h3>
                    <p class="text-brand-gray">&#8358;15,000</p>
                </div>
            </div>
            <!-- Product Card 2 (Placeholder) -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                 <div class="relative">
                    <img src="https://placehold.co/400x400/e0e0e0/1a1a1a?text=Design+2" alt="Monogram Design 2" class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="product-details.php?id=2" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-brand-dark">Art Deco Initial</h3>
                    <p class="text-brand-gray">&#8358;12,500</p>
                </div>
            </div>
            <!-- Product Card 3 (Placeholder) -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                 <div class="relative">
                    <img src="https://placehold.co/400x400/cccccc/1a1a1a?text=Design+3" alt="Monogram Design 3" class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="product-details.php?id=3" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-brand-dark">Minimalist Script</h3>
                    <p class="text-brand-gray">&#8358;10,000</p>
                </div>
            </div>
            <!-- Product Card 4 (Placeholder) -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                 <div class="relative">
                    <img src="https://placehold.co/400x400/b0b0b0/1a1a1a?text=Design+4" alt="Monogram Design 4" class="w-full h-64 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="product-details.php?id=4" class="text-white border-2 border-brand-gold bg-brand-gold bg-opacity-50 py-2 px-6 rounded-full hover:bg-opacity-100 transition-all">View Details</a>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold text-brand-dark">Floral Emblem</h3>
                    <p class="text-brand-gray">&#8358;18,000</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom Service Section -->
<section class="py-16 bg-brand-light-gray">
    <div class="container mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2">
            <img src="https://placehold.co/600x400/333333/FFD700?text=Bespoke+Service" alt="Tailor working on a design" class="rounded-lg shadow-2xl">
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
