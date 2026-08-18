$(document).ready(function() {
    // --- Modal Logic ---
    var modal = $('#contact-modal');
    var btn = $('#open-form-btn');
    var span = $('.close-btn');

    // Open the modal
    btn.on('click', function() {
        modal.css('display', 'block');
    });

    // Close the modal when clicking on (x)
    span.on('click', function() {
        modal.css('display', 'none');
    });

    // Close the modal when clicking outside of it
    $(window).on('click', function(event) {
        if (event.target.id === 'contact-modal') {
            modal.css('display', 'none');
        }
    });

    // --- Form Submission Logic (using AJAX) ---
    $('#contact-form').on('submit', function(e) {
        e.preventDefault(); // Stop the default form submission

        var formData = $(this).serialize(); // Serialize form data for AJAX

        // Show loading state
        $('#submit-btn').text('Sending...').prop('disabled', true);
        $('#form-message').removeClass('success error').text('').hide();

        $.ajax({
            type: 'POST',
            url: 'send_email.php', // *** THIS IS THE PHP FILE THAT HANDLES THE MAIL ***
            data: formData,
            dataType: 'json', // Expecting a JSON response from PHP
            success: function(response) {
                // Handle success or failure message from PHP
                if (response.status === 'success') {
                    $('#form-message').addClass('success').text('Message sent successfully!').show();
                    $('#contact-form')[0].reset(); // Clear the form
                } else {
                    // Display error message from PHP (e.g., 'Invalid email address')
                    $('#form-message').addClass('error').text(response.message || 'An unknown error occurred.').show();
                }
            },

            error: function(xhr, status, error) {
                // Handle a full AJAX/server error (e.g., 500 error, file not found)
                
                console.error("AJAX Error Details:");
                console.error("HTTP Status: " + xhr.status + " (" + error + ")");
                
                // Display the raw response text, which usually contains the PHP error message
                console.error("Server Response Text (Look for PHP errors here):");
                console.log(xhr.responseText); 
                
                // Display generic message on the page
                $('#form-message').addClass('error').text('Could not connect to the server. Check console for error details (F12).').show();
            },
            
            complete: function() {
                // Reset button text/state after completion
                $('#submit-btn').text('Send Message').prop('disabled', false);
            }
        });
    });
});