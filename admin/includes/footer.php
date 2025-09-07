            </main>
        </div>
    </div>

    <!-- Main Admin JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../assets/js/admin.js"></script>
    
    <?php
    // Get the current script name (e.g., 'dashboard.php')
    $current_page = basename($_SERVER['PHP_SELF']);

    // Conditionally load page-specific JavaScript for better performance
    if ($current_page == 'dashboard.php') {
     //   echo '<script src="../assets/js/dashboard.js"></script>';
    }
    // Future page-specific scripts can be added here
    // elseif ($current_page == 'manage_users.php') {
    //     echo '<script src="../assets/js/users.js"></script>';
    // }
    ?>
</body>
</html>

