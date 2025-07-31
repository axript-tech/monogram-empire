<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('assets/images/hero-bg.jpg');"></div>
    <div class="relative container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold" style="font-family: 'Playfair Display', serif;">Get In Touch</h1>
        <p class="text-lg text-gray-300 mt-2">We'd love to hear from you. Let's create something beautiful together.</p>
    </div>
</div>

<!-- Contact Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-brand-light-gray p-8 rounded-lg shadow-lg">
                <h2 class="text-3xl font-bold text-brand-dark mb-6">Send Us a Message</h2>
                
                <!-- Container for AJAX messages -->
                <div id="contact-message-container" class="mb-4" style="display: none;"></div>

                <form id="contact-form" action="api/contact.php" method="POST">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="John Doe" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-bold mb-2">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-4">
                        <label for="subject" class="block text-gray-700 font-bold mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="e.g., Custom Order Inquiry" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                    </div>
                    <div class="mb-6">
                        <label for="message" class="block text-gray-700 font-bold mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Your message here..." required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-gold"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-brand-gold text-brand-dark font-bold py-3 px-8 rounded-full text-lg hover:bg-yellow-300 transition-transform transform hover:scale-105">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contact Info & Map -->
            <div>
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-brand-dark mb-4">Contact Information</h3>
                    <div class="space-y-4 text-gray-600">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-brand-gold text-xl mt-1 mr-4"></i>
                            <span>123 Fashion Avenue, Victoria Island, Lagos, Nigeria</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-phone text-brand-gold text-xl mt-1 mr-4"></i>
                            <span>+234 801 234 5678</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-brand-gold text-xl mt-1 mr-4"></i>
                            <span>contact@monogramempire.com</span>
                        </div>
                         <div class="flex items-start">
                            <i class="fas fa-clock text-brand-gold text-xl mt-1 mr-4"></i>
                            <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                        </div>
                    </div>
                </div>
                
                <div>
                     <h3 class="text-2xl font-bold text-brand-dark mb-4">Find Us Here</h3>
                     <div class="w-full h-80 rounded-lg overflow-hidden shadow-2xl">
                        <!-- Placeholder for an interactive map -->
                        <img src="https://placehold.co/800x600/333333/FFD700?text=Map+of+Lagos" alt="Map showing our location in Lagos" class="w-full h-full object-cover">
                     </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
