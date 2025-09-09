$(document).ready(function() {

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

        const submitButton = $(this).find('button[type="submit"]');
        const buttonText = submitButton.find('.button-text');
        const buttonSpinner = submitButton.find('.button-spinner');
        
        buttonText.hide();
        buttonSpinner.show();
        submitButton.prop('disabled', true);

        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            confirm_password: $('#confirm_password').val()
        };

        if (formData.password !== formData.confirm_password) {
            showAuthMessage("Passwords do not match.", false);
            buttonText.show();
            buttonSpinner.hide();
            submitButton.prop('disabled', false);
            return;
        }
        if (!$('input[type=checkbox]').is(':checked')) {
            showAuthMessage("You must agree to the terms and conditions.", false);
            buttonText.show();
            buttonSpinner.hide();
            submitButton.prop('disabled', false);
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
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                if (response && response.message) {
                    const errorMessage = response.errors ? response.errors.join('<br>') : response.message;
                    showAuthMessage(errorMessage, false);
                } else {
                    showAuthMessage('An unexpected error occurred. Please try again later.', false);
                }
            },
            complete: function() {
                buttonText.show();
                buttonSpinner.hide();
                submitButton.prop('disabled', false);
            }
        });
    });

    /**
     * Handles the submission of the login form.
     */
   
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();

        const submitButton = $(this).find('button[type="submit"]');
        const buttonText = submitButton.find('.button-text');
        const buttonSpinner = submitButton.find('.button-spinner');

        buttonText.hide();
        buttonSpinner.show();
        submitButton.prop('disabled', true);

        const formData = {
            email: $('#email').val(),
            password: $('#password').val(),
            // Include the redirect URL in the data sent to the API
            redirect_url: $('input[name="redirect_url"]').val()
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
                    // Use the redirect URL from the API, or default to order history
                    const redirectTarget = response.redirect_url || 'order-history.php';
                    setTimeout(() => window.location.href = redirectTarget, 1500);
                } else {
                    showAuthMessage(response.message, false);
                    $('#password').val('');
                }
            },
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                if (response && response.message) {
                    showAuthMessage(response.message, false);
                } else {
                    showAuthMessage('An unexpected error occurred. Please try again later.', false);
                }
                $('#password').val('');
            },
            complete: function() {
                buttonText.show();
                buttonSpinner.hide();
                submitButton.prop('disabled', false);
            }
        });
    });


    /**
     * Handles the submission of the forgot password form.
     */
    $('#forgot-password-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();
        const submitButton = $(this).find('button[type="submit"]');
        const buttonText = submitButton.find('.button-text');
        const buttonSpinner = submitButton.find('.button-spinner');

        buttonText.hide();
        buttonSpinner.show();
        submitButton.prop('disabled', true);
        
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
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                if (response && response.message) {
                    showAuthMessage(response.message, false);
                } else {
                    showAuthMessage('An unexpected error occurred.', false);
                }
            },
            complete: function() {
                buttonText.show();
                buttonSpinner.hide();
                submitButton.prop('disabled', false);
            }
        });
    });

    /**
     * Handles the submission of the reset password form.
     */
    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        $('#auth-message-container').slideUp().empty();
        
        const submitButton = $(this).find('button[type="submit"]');
        const buttonText = submitButton.find('.button-text');
        const buttonSpinner = submitButton.find('.button-spinner');
 
        buttonText.hide();
        buttonSpinner.show();
        submitButton.prop('disabled', true);

        const formData = {
            token: $('input[name="token"]').val(),
            email: $('input[name="email"]').val(),
            new_password: $('#new_password').val(),
            confirm_new_password: $('#confirm_new_password').val()
        };

        if (formData.new_password !== formData.confirm_new_password) {
            showAuthMessage("New passwords do not match.", false);
            buttonText.show();
            buttonSpinner.hide();
            submitButton.prop('disabled', false);
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
            error: function(jqXHR) {
                const response = jqXHR.responseJSON;
                if (response && response.message) {
                    showAuthMessage(response.message, false);
                } else {
                    showAuthMessage('An unexpected error occurred. Please try again later.', false);
                }
            },
            complete: function() {
                buttonText.show();
                buttonSpinner.hide();
                submitButton.prop('disabled', false);
            }
        });
    });

});

