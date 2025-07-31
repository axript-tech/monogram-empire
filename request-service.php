<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Bespoke Monogram Service</h1>
        <p class="text-lg text-gray-300 mt-2">Let us craft a unique design that tells your story.</p>
    </div>
</div>

<!-- Custom Service Request Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Form -->
            <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                <h2 class="text-3xl font-bold text-brand-dark mb-6">Submit Your Request</h2>
                <form id="service-request-form" action="api/preorder/submit.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="John Doe" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-4">
                        <label for="initials" class="block text-gray-700 font-bold mb-2">Initials to Include</label>
                        <input type="text" id="initials" name="initials" placeholder="e.g., J.D. or JD" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-4">
                        <label for="style_preference" class="block text-gray-700 font-bold mb-2">Style Preference</label>
                        <select id="style_preference" name="style_preference" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                            <option>Classic & Traditional</option>
                            <option>Modern & Minimalist</option>
                            <option>Floral & Botanical</option>
                            <option>Geometric</option>
                            <option>Other (describe below)</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="details" class="block text-gray-700 font-bold mb-2">Design Details & Inspiration</label>
                        <textarea id="details" name="details" rows="5" placeholder="Describe your vision. Mention any specific elements, themes, or feelings you want the design to evoke." required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold"></textarea>
                    </div>
                    <div class="mb-6">
                        <label for="inspiration_files" class="block text-gray-700 font-bold mb-2">Inspiration Files (Optional)</label>
                        <input type="file" id="inspiration_files" name="inspiration_files[]" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-gold file:text-brand-dark hover:file:bg-yellow-300">
                        <p class="text-xs text-gray-500 mt-1">Upload images, sketches, or documents (.jpg, .png, .pdf).</p>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                            Request a Quote
                        </button>
                    </div>
                </form>
            </div>
            <!-- How It Works -->
            <div class="prose lg:prose-lg">
                <h2 class="text-3xl font-bold text-brand-dark" style="font-family: 'Playfair Display', serif;">How It Works</h2>
                <ol>
                    <li>
                        <strong>Submit Your Vision</strong><br>
                        Fill out the form with your ideas, initials, and any inspirational images. The more detail, the better!
                    </li>
                    <li>
                        <strong>Receive a Quote</strong><br>
                        Our design team will review your request and send you a detailed quote and project timeline within 2-3 business days.
                    </li>
                    <li>
                        <strong>Collaborate & Refine</strong><br>
                        Once you approve the quote, we'll begin the design process. You'll receive initial concepts and have the opportunity to provide feedback and request revisions.
                    </li>
                    <li>
                        <strong>Final Delivery</strong><br>
                        After your final approval, we will deliver your unique, high-resolution monogram files in all standard formats, ready for you to use.
                    </li>
                </ol>
                <div class="mt-8">
                    <p>Already have a request? <a href="track-preorder.php" class="text-brand-gold font-bold hover:underline">Track it here.</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
.prose ol { list-style-type: decimal; padding-left: 1.5rem; }
.prose li { margin-bottom: 1.5rem; padding-left: 0.5rem; }
.prose li strong { color: #1a1a1a; }
</style>
