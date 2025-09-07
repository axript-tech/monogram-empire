<?php 
// This MUST be the very first line of the file.
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="relative bg-brand-dark text-white py-20">
    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2070&auto=format&fit=crop');"></div>
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
            <div class="bg-gray-50 p-8 rounded-lg shadow-lg">
                <h2 class="text-3xl font-bold text-brand-dark mb-6">Send Us a Message</h2>
                
                <!-- Container for AJAX messages -->
                <div id="contact-message-container" class="mb-4" style="display: none;"></div>

                <form id="contact-form">
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
                            <i class="fas fa-phone-alt text-brand-gold text-xl mt-1 mr-4"></i>
                            <div>
                                <p class="font-bold">Call/Whatsapp</p>
                                <p>08101583986</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-brand-gold text-xl mt-1 mr-4"></i>
                            <div>
                                <p class="font-bold">Oshodi Office</p>
                                <p>79 Oshodi Road, Oshodi, Lagos</p>
                            </div>
                        </div>
                         <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-brand-gold text-xl mt-1 mr-4"></i>
                              <div>
                                <p class="font-bold">Agege Office</p>
                                <p>95, Old Abeokuta Road, Misamsco Plaza opposite post office, Agege, Lagos</p>
                            </div>
                        </div>
                         <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-brand-gold text-xl mt-1 mr-4"></i>
                              <div>
                                <p class="font-bold">Ogba Office</p>
                                <p>1 Ogunsola Street, Aguda, Ogba, Lagos</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                   <h3 class="text-2xl font-bold text-brand-dark mb-4">Find Us Here</h3>
                   <div class="w-full h-80 rounded-lg overflow-hidden shadow-2xl">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.591399898037!2d3.334016915305981!3d6.572793824424078!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8e7b1f5b2e4d%3A0x8c7729b27a3a992d!2s79%20Oshodi%20Rd%2C%20Oshodi-Isolo%2C%20Lagos!5e0!3m2!1sen!2sng!4v1662552829363!5m2!1sen!2sng" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                   </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

