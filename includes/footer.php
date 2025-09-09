<?php
    // Simple Visitor Counter Logic
    // This will create a 'visitor_count.txt' file in the same directory as the script executing it.
    // Ensure your server has write permissions for this directory.
    $counterFile = 'visitor_count.txt';

    // Check if the file exists, if not, create it.
    if (!file_exists($counterFile)) {
        file_put_contents($counterFile, '0');
    }

    // Read the current count, increment it, and save it back.
    $visitorCount = (int)file_get_contents($counterFile);
    $visitorCount++;
    file_put_contents($counterFile, $visitorCount);
?>
    </main>
    <footer class="bg-brand-dark text-gray-300 pt-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About Section -->
                <div>
                    <h3 class="text-lg font-bold text-white mb-4"><span class="text-brand-gold">Monogram</span>Empire</h3>
                    <p class="text-sm text-gray-400">Exquisite monogram designs, tailored for the modern connoisseur. Your identity, elegantly stitched in time.</p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="about.php" class="hover:text-brand-gold transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="hover:text-brand-gold transition-colors">Contact</a></li>
                        <li><a href="faq.php" class="hover:text-brand-gold transition-colors">FAQ</a></li>
                        <li><a href="order-history.php" class="hover:text-brand-gold transition-colors">Order History</a></li>
                    </ul>
                </div>
                <!-- Legal -->
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="terms.php" class="hover:text-brand-gold transition-colors">Terms & Conditions</a></li>
                        <li><a href="terms.php#privacy" class="hover:text-brand-gold transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Join Our Newsletter</h3>
                    <p class="text-sm text-gray-400 mb-4">Get exclusive offers and the latest design drops.</p>
                    <form action="#" method="POST">
                        <div class="flex">
                            <input type="email" placeholder="Your Email" class="w-full px-4 py-2 bg-brand-gray border border-gray-600 rounded-l-md focus:outline-none focus:ring-2 focus:ring-brand-gold text-white">
                            <button type="submit" class="bg-brand-gold text-brand-dark px-4 py-2 rounded-r-md font-bold hover:bg-yellow-300 transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-8 py-6 border-t border-gray-700 flex flex-col md:flex-row justify-between items-center text-sm">
                <p class="text-gray-500 mb-4 md:mb-0">&copy; <?php echo date("Y"); ?> Monogram Empire. All Rights Reserved.</p>
                
                <div class="flex flex-col items-center mb-4 md:mb-0">
                    <div class="flex space-x-4 mb-2">
                        <a href="#" class="text-gray-400 hover:text-brand-gold transition-colors"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-brand-gold transition-colors"><i class="fab fa-pinterest fa-lg"></i></a>
                        <a href="#" class="text-gray-400 hover:text-brand-gold transition-colors"><i class="fab fa-facebook-f fa-lg"></i></a>
                    </div>
                    <div class="flex items-center space-x-2 text-gray-500">
                        <i class="fas fa-eye"></i>
                        <span>Visitors: <?php echo number_format($visitorCount); ?></span>
                    </div>
                </div>

                <p class="text-gray-500">Developed by <a href="https://axript.com.ng" target="_blank" rel="noopener noreferrer" class="text-brand-gold hover:underline">Axript Tech</a></p>
            </div>
        </div>
    </footer>
      <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/2348101583986" target="_blank" rel="noopener noreferrer" class="fixed bottom-8 right-8 bg-green-500 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-transform transform hover:scale-110 z-50">
        <i class="fab fa-whatsapp fa-2x"></i>
    </a>

    <!-- Back to Top Button -->
    <a href="#" id="back-to-top" class="fixed bottom-8 left-8 bg-brand-dark text-brand-gold w-12 h-12 rounded-full flex items-center justify-center shadow-lg hover:bg-gray-700 transition-all z-50 hidden p-4">
        <i class="fas fa-arrow-up"></i>
    </a>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/shop.js"></script>
    <script src="assets/js/cart.js"></script>
    <script src="assets/js/auth.js"></script>
  

</body>
</html>