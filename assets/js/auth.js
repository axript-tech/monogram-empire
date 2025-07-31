$(document).ready(function() {

    /**
     * Handles the submission of the registration form.
     */
    $('#register-form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Clear previous messages
        // You would have a dedicated element for status messages
        // For now, we'll use alerts for simplicity, but this is bad practice for production.
        
        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            confirm_password: $('#confirm_password').val()
        };

        // Basic frontend validation
        if (formData.password !== formData.confirm_password) {
            alert("Passwords do not match.");
            return;
        }
        if (!$('input[type=checkbox]').is(':checked')) {
            alert("You must agree to the terms and conditions.");
            return;
        }

        $.ajax({
            url: 'api/auth/register.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Redirect to the home page or a welcome page on successful registration
                    window.location.href = 'index.php';
                } else {
                    // Display error messages
                    alert('Error: ' + response.message + '\n' + (response.errors ? response.errors.join('\n') : ''));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle server errors (e.g., 500 Internal Server Error)
                alert('An unexpected error occurred. Please try again later.');
                console.error(textStatus, errorThrown);
            }
        });
    });

    /**
     * Handles the submission of the login form.
     */
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            email: $('#email').val(),
            password: $('#password').val()
        };

        $.ajax({
            url: 'api/auth/login.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Redirect to the user's account/dashboard or home page
                    window.location.href = 'order-history.php';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An unexpected error occurred. Please try again later.');
            }
        });
    });

    /**
     * Handles the submission of the forgot password form.
     */
    $('#forgot-password-form').on('submit', function(e) {
        e.preventDefault();
        const formData = { email: $('#email').val() };

        $.ajax({
            url: 'api/auth/forgot_password.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                // Always show a generic success message for security
                alert(response.message);
            },
            error: function() {
                alert('An unexpected error occurred.');
            }
        });
    });

    /**
     * Handles the submission of the reset password form.
     */
    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            token: $('input[name="token"]').val(),
            email: $('input[name="email"]').val(),
            new_password: $('#new_password').val(),
            confirm_new_password: $('#confirm_new_password').val()
        };

        if (formData.new_password !== formData.confirm_new_password) {
            alert("New passwords do not match.");
            return;
        }

        $.ajax({
            url: 'api/auth/reset_password.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = 'login.php';
                } else {
                    alert('Error: ' + response.message);
                }$(document).ready(function() {

    /**
     * Helper function to display success or error messages within the form.
     * @param {string} message - The message to display.
     * @param {boolean} isSuccess - True for a success message, false for an error.
     */
    function showAuthMessage(message, isSuccess) {
        const messageContainer = $('#auth-message-container');
        const successClasses = 'bg-green-100 border-green-400 text-green-700';
        const errorClasses = 'bg-red-100 border-red-400 text-red-700';

        messageContainer.html(`<div class="border px-4 py-3 rounded relative ${isSuccess ? successClasses : errorClasses}" role="alert">${message}</div>`);
        messageContainer.slideDown();
    }

    /**
     * Handles the submission of the registration form.
     */
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();

        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            confirm_password: $('#confirm_password').val()
        };

        if (formData.password !== formData.confirm_password) {
            showAuthMessage("Passwords do not match.", false);
            return;
        }
        if (!$('input[type=checkbox]').is(':checked')) {
            showAuthMessage("You must agree to the terms and conditions.", false);
            return;
        }

        $.ajax({
            url: 'api/auth/register.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAuthMessage(response.message, true);
                    setTimeout(() => window.location.href = 'index.php', 2000);
                } else {
                    const errorMessage = response.errors ? response.errors.join('<br>') : response.message;
                    showAuthMessage(errorMessage, false);
                }
            },
            error: function() {
                showAuthMessage('An unexpected error occurred. Please try again later.', false);
            }
        });
    });

    /**
     * Handles the submission of the login form.
     */
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();

        const formData = {
            email: $('#email').val(),
            password: $('#password').val()
        };

        $.ajax({
            url: 'api/auth/login.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAuthMessage(response.message, true);
                    setTimeout(() => window.location.href = 'order-history.php', 1500);
                } else {
                    showAuthMessage(response.message, false);
                }
            },
            error: function() {
                showAuthMessage('An unexpected error occurred. Please try again later.', false);
            }
        });
    });

    /**
     * Handles the submission of the forgot password form.
     */
    $('#forgot-password-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();
        const formData = { email: $('#email').val() };

        $.ajax({
            url: 'api/auth/forgot_password.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                showAuthMessage(response.message, true);
            },
            error: function() {
                showAuthMessage('An unexpected error occurred.', false);
            }
        });
    });

    /**
     * Handles the submission of the reset password form.
     */
    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();
        
        const formData = {
            token: $('input[name="token"]').val(),
            email: $('input[name="email"]').val(),
            new_password: $('#new_password').val(),
            confirm_new_password: $('#confirm_new_password').val()
        };

        if (formData.new_password !== formData.confirm_new_password) {
            showAuthMessage("New passwords do not match.", false);
            return;
        }

        $.ajax({
            url: 'api/auth/reset_password.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAuthMessage(response.message, true);
                    setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    showAuthMessage(response.message, false);
                }
            },
            error: function() {
                showAuthMessage('An unexpected error occurred.', false);
            }
        });
    });

});

            },
            error: function() {
                alert('An unexpected error occurred.');
            }
        });
    });

});
