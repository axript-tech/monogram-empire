$(document).ready(function() {
    const formContainer = $('#request-form-container');
    const successContainer = $('#success-container');
    const form = $('#preorder-form');
    const messageContainer = $('#preorder-message-container');
    const submitButton = form.find('button[type="submit"]');
    const buttonText = submitButton.find('.button-text');
    const buttonSpinner = submitButton.find('.button-spinner');

    function showMessage(message, isSuccess) {
        const successClasses = 'bg-green-100 border-green-400 text-green-700';
        const errorClasses = 'bg-red-100 border-red-400 text-red-700';
        const messageHtml = `<div class="border px-4 py-3 rounded relative ${isSuccess ? successClasses : errorClasses}" role="alert">${message}</div>`;
        
        messageContainer.html(messageHtml).slideDown();
        setTimeout(() => messageContainer.slideUp(), 5000);
    }

    form.on('submit', function(e) {
        e.preventDefault();
        messageContainer.slideUp().empty();
        
        // Show loading state
        buttonText.text('Submitting...');
        buttonSpinner.show();
        submitButton.prop('disabled', true);

        // Use FormData for file uploads
        const formData = new FormData(this);

        $.ajax({
            url: 'api/preorder/submit.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false, // Important for file uploads
            processData: false, // Important for file uploads
            success: function(response) {
                if (response.success) {
                    // Hide form and show success message
                    formContainer.fadeOut(() => {
                        $('#success-tracking-id').text(response.tracking_id);
                        $('#success-track-link').attr('href', `track-preorder.php?tracking_id=${response.tracking_id}`);
                        successContainer.fadeIn();
                    });
                } else {
                    const errorMessage = response.errors ? response.errors.join('<br>') : response.message;
                    showMessage(errorMessage, false);
                }
            },
            error: function(jqXHR) {
                if(jqXHR.status === 401) {
                     showMessage('You must be logged in to submit a request.', false);
                     setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    showMessage('An unexpected error occurred. Please try again.', false);
                }
            },
            complete: function() {
                // Restore button state
                buttonText.text('Request a Quote');
                buttonSpinner.hide();
                submitButton.prop('disabled', false);
            }
        });
    });

    // Handle "Make Another Request" button click
    $('#make-another-request-btn').on('click', function() {
        successContainer.fadeOut(() => {
            form[0].reset(); // Reset the form fields
            formContainer.fadeIn();
        });
    });
});

