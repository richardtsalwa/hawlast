<?php
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lilian Koskei - Digital Consultant">
  <meta name="keywords" content="Lish">
<title>Lilian Koskei - Hospitality & Airbnb Strategist</title>
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
<link rel="shortcut icon" href="images/favicon.ico">
<link rel="stylesheet" href="style.css?mmm=yuuu">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script 
  src="https://www.paypal.com/sdk/js?client-id=BAA1EYXuF79Jt2L9IfdLKxpz0lj5vIaKRxqKkL5MrkZg85ElRU0-Mj-uwZat-_1nVN7nInoO8Zd82Knkjw&components=hosted-buttons&disable-funding=venmo&currency=USD">
</script>
<style>
    /* Styling to center the PayPal button within its wrapper */
#paypal-buttons-wrapper {
    /* Centers the 'Or pay instantly via PayPal:' text */
    text-align: center;
}

/* Targets the individual PayPal button container */
.paypal-hosted-button {
    /* Ensures the button itself is centered horizontally */
    margin: 10px auto; 
    /* You may need to set a max-width if the button stretches too wide */
    max-width: 350px; 
}

/* --- Discount Styling --- */

/* 1. Original Price Strikethrough */
.original-price-strike {
    text-decoration: line-through;
    color: #999; /* Grey color */
    font-size: 0.9em;
    margin-right: 5px;
}

/* 2. Highlighted Discounted Price */
.discounted-price {
    color: #cc0000; /* Red color to emphasize the deal */
    font-weight: bold;
    font-size: 1.1em;
}

/* 3. Discount Badge */
.discount-badge {
    display: inline-block;
    background-color: #cc0000; /* Red background */
    color: white;
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8em;
    margin-bottom: 5px;
    margin-left: 10px;
    /* Optional: Small rotation for a "sticker" effect */
    transform: rotate(-3deg); 
}


</style>
</head>
<body>
<header>
        <h1>Lilian Koskei</h1>
        <h2>Hospitality & Airbnb Strategist</h2>
        <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#profile">About Me</a></li>
            <li><a href="#services">Our services</a></li>
            <li><a href="#contact-info">Contact</a></li>
        </ul>
    </nav>
    
    </header>
<main>
    <section id="profile" class="about-us-section">
    <div class="about-container"> <h2 class="section-title">About Me & My Hosting Journey</h2>
        
        <div class="about-row"> <div class="col-50-left about-text-content mb-4 mb-lg-0">
                <p>I have been a dedicated Airbnb host for over five years, and my greatest joy is seeing guests relax and truly enjoy their stay in my home here in Kenya. I am honored that many guests return to my space every time they visit our country.</p>
                <p>Over the years, I've watched many promising hosts enter the Airbnb community only to become discouraged and eventually quit. I truly believe that everyone can thrive in this space with the right foundation and guidance.</p>
                <p>That conviction is what inspired me to create this guide.</p>
                <p>So, buckle up! Join me as I share my proven experience and actionable guidance to help you transform your hosting journey. Cheers to happy and successful hosting!</p>
                <a href="#services" class="btn btn-primary mt-3">View My Services</a>
            </div>
            
            <div class="col-50-right text-center">
                <img src="images/lilian-koskei-profile.jpeg" alt="Lilian Koskei - Airbnb Hosting Expert" class="img-fluid about-image">
            </div>
            
        </div>
    </div>
</section>

<section id="services">
  <h2>Our Airbnb Host Consulting Services</h2>
  <p>Select one or more services. Once payment is confirmed, you will receive the ebook or chance to schedule a consultation meeting.</p>

<div id="pdf-list" class="service-grid">
  <form id="service-form">
    
    <div class="service-item">
      <img src="images/service-1.png" alt="HACK THE AIRBNB ALGORITHM">
      <p><strong>Hack the Airbnb Algorithm</strong><br>
        The best how-to guide to Hack the Airbnb Algorithm.<B>PDF Guide</B> 
      </p>
      <div class="discount-badge">-<?php echo $discountPercentageEbook; ?>% OFF!</div>
      
      <label>
<input type="checkbox" class="pdf-checkbox" data-price="<? echo $service1_usd_discounted; ?>" data-ref="LISH1" data-name="Hack the Airbnb Algorithm" data-type="pdf">

<span class="price-usd original-price-strike">
            <?php echo $currencyUSD; echo $service1_original_usd; ?>
        </span>
        
        <span class="price-usd discounted-price">
            <?php echo $currencyUSD; echo $service1_usd_discounted; ?>
        </span> /
        
        <span class="price-kes discounted-price-kes">
            <?php echo $currencyKES; echo $service1_kes_discounted; ?>
        </span> 
    </label>
    
     
    </div>

    <div class="service-item">
      <img src="images/service-3.png" alt="Pick my brain">
      <p><strong>Pick my brain </strong><br>
        Get one-on-one with me. Schedule time for the online call after the realtime payment.
      </p>
      <label>
<input type="checkbox" class="pdf-checkbox" data-price="<? echo $service2_usd; ?>" data-ref="LISH2" data-name="Pick my brain" data-type="pdf">
  <span class="price-usd"><?php echo $currencyUSD; echo $service2_usd; ?></span> /
 <span class="price-kes"><?php echo $currencyKES; echo $service2_usd*$exchangeRateUSD; ?></span> 
      </label>
    </div>

    <div class="service-item">
      <img src="images/service-2.png" alt="Airbnb Set Up from start to finish">
      <p><strong>Airbnb Set Up from start to finish</strong><br>
      We guide you through the entire process: location, decor, listing, and managing your first booking.
      </p>
      <label>
<input type="checkbox" class="pdf-checkbox" data-price="<? echo $service3_usd; ?>" data-ref="LISH3"  data-name="Hack the Airbnb Algorithm" data-type="pdf">
  <span class="price-usd"><?php echo $currencyUSD; echo $service3_usd; ?></span> /
 <span class="price-kes"><?php echo $currencyKES; echo $service3_usd*$exchangeRateUSD; ?></span> 
      </label>
    </div>
    </form>
</div>

<?php
// Define the Hosted Button IDs for possible totals (MUST be whole numbers)
// $service1_usd=10, $service2_usd=20, $service3_usd=30 (Example prices)
$paypal_buttons = [
    10 => 'P8L6KUBR6MXUU', 
    24 => 'A2E7CYZK9T5WX',
    30 => 'L4J8BPAX0Q1VV',
    40 => 'H5G9CXPY3R7BB',
    50 => 'Z1B3V7QW5M9EE',
    60 => 'K6T0J4XF8D2CC',
    // ... all other combinations ...
];
// Embed the exchange rate for JavaScript use
$exchangeRate = (float)$exchangeRateUSD; 
?>

<div class="total-box">
    <p>Total: 
        <span class="price-usd total-usd-display"><?php echo $currencyUSD; ?><span id="total-amount-usd">0.00</span></span>
        / 
        <span class="price-kes total-kes-display"><?php echo $currencyKES; ?><span id="total-amount-kes">0</span></span>
    </p>

    <button type="button" id="buy-btn">Pay via MPESA</button> <BR>
    
    <div id="paypal-buttons-wrapper" style="margin-top: 15px;">
        <p style="font-size: 0.9em; margin-bottom: 5px;">Or pay instantly via PayPal:</p>
        
        <div id="paypal-button-container">
            <?php foreach ($paypal_buttons as $amount => $hostedButtonId): ?>
                <div 
                    id="paypal-container-<?php echo $hostedButtonId; ?>" 
                    data-amount="<?php echo $amount; ?>" 
                    class="paypal-hosted-button" 
                    style="display: none;" 
                ></div>
                <script>
                  paypal.HostedButtons({
                    hostedButtonId: "<?php echo $hostedButtonId; ?>",
                  }).render("#paypal-container-<?php echo $hostedButtonId; ?>")
                </script>
            <?php endforeach; ?>
            <p id="paypal-placeholder" style="color: #999; font-size: 0.9em; text-align: center;">Select a service to see payment options.</p>
        </div>
    </div>
</div>


</section>

        <hr>

        <section id="contact-info">
            <h3>Get in Touch </h3>
            <p>Feel free to reach out for inquiries, suggestions, or support.</p>
<div class="contact-links">
                <a href="tel:<?PHP echo $businessPhone; ?>" class="contact-button phone-link">Call Me (Phone)</a>
                <a href="https://wa.me/<?php echo $whatsappnumber; ?>" target="_blank" class="contact-button whatsapp-link">WhatsApp Me</a>
                <button id="open-form-btn" class="contact-button email-link">Send Me an Email</button>
            </div>
        </section>
        
    </main>
    <footer>
<?php

$startYear = 2025;
$currentYear = date('Y');

// Check if the current year is greater than the start year
if ($currentYear > $startYear) {
    $copyrightYear = $startYear . ' - ' . $currentYear;
} else {

    $copyrightYear = $startYear;
}

// Echo the final copyright string
echo 'Copyright &copy; ' . $copyrightYear . ' All Rights Reserved.';

?>

  </footer>

    <div id="contact-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h4>Contact <?php echo $business; ?></h4>
            <form id="contact-form">
                <div class="form-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" id="submit-btn">Send Message</button>
                <div id="form-message"></div>
            </form>
        </div>
    </div>

<!-- Modal: collects phone & email -->
<div id="pay-modal" class="modal">
  <div class="modal-content">
    <span class="close-btn" id="close-modal">&times;</span>
    <h3>MPESA Payment Details</h3>
    <div class="form-group">
      <label for="phone">MPESA Phone number (start 0, e.g. 0720200200)</label>
      <input type="text" id="phone" placeholder="0..." />
    </div>
<div class="form-group">
 <label for="pay-email">Your Email Address</label>
<input type="email" id="pay-email" value="" placeholder="you@example.com" /> 
</div>
    <div class="form-group" style="display: none;">
      <label for="account">Reference</label>
      <input type="text" id="account" value="LIL0011" />
    </div>
    <button id="confirm-pay">Send Payment Prompt</button>
    <div id="form-message"></div>
  </div>
</div>

<script>
$(function(){
    // Ensure the exchange rate is available from the PHP environment
    // NOTE: This assumes $exchangeRateUSD is embedded in your HTML head or before this script.
    // Example: const exchangeRate = 145; 
    const exchangeRate = <?php echo (float)$exchangeRateUSD; ?>; 

    // PayPal configuration constants (for clear identification)
    const $paypalButtons = $('.paypal-hosted-button');
    const $paypalPlaceholder = $('#paypal-placeholder');

    // Function to calculate and update all totals (USD & KES) and control PayPal button visibility
    function updateTotalsAndPaypal(){
        let totalUSD = 0;
        
        // 1. Calculate Total USD (using the corrected data-price attribute)
        $('.pdf-checkbox:checked').each(function(){
            // The data-price attribute MUST now hold the USD amount
            totalUSD += parseFloat($(this).data('price'));
        });

        // Round the total USD to the nearest whole number for Hosted Button matching
        // and fixed two decimal places for display
        const roundedTotalUSD = Math.round(totalUSD); 
        const displayTotalUSD = totalUSD.toFixed(2);
        
        // 2. Calculate Total KES
        const totalKES = Math.round(totalUSD * exchangeRate);

        // 3. Update Display Elements
        $('#total-amount-usd').text(displayTotalUSD);
        $('#total-amount-kes').text(totalKES);

        // 4. Update M-Pesa Button Text and Data
        if (totalKES > 0) {
            $('#buy-btn').text(`Pay ${totalKES} KES via MPESA`);
            // Store the KES total on the button for the modal click handler
            $('#buy-btn').data('amount-kes', totalKES);
        } else {
            $('#buy-btn').text('Pay via MPESA');
            $('#buy-btn').data('amount-kes', 0);
        }

        // 5. PayPal Button Visibility Logic
        if (roundedTotalUSD > 0) {
            let buttonFound = false;
            
            $paypalButtons.each(function() {
                const $this = $(this);
                const buttonAmount = parseFloat($this.data('amount'));
                
                if (buttonAmount === roundedTotalUSD) {
                    // Show the matching button
                    $this.show();
                    $paypalPlaceholder.hide();
                    buttonFound = true;
                } else {
                    // Hide all non-matching buttons
                    $this.hide();
                }
            });
            
            if (!buttonFound) {
                $paypalButtons.hide();
                $paypalPlaceholder.show().text(`Error: PayPal button not configured for $${roundedTotalUSD} USD.`);
            }
        } else {
            // No services selected
            $paypalButtons.hide();
            $paypalPlaceholder.show().text('Select a service to see payment options.');
        }

        // Return the KES total for the M-Pesa click handler below
        return totalKES;
    }

    // Run on page load and on checkbox change
    updateTotalsAndPaypal(); 
    $('.pdf-checkbox').on('change', updateTotalsAndPaypal);

    // --- M-PESA LOGIC UPDATED ---

    // 1. M-Pesa Modal Opening Logic
    $('#buy-btn').on('click', function(){
        // Get the total KES directly from the data attribute set by updateTotalsAndPaypal()
        const total = $(this).data('amount-kes'); 
        
        if (total <= 0 || total === undefined) {
            alert('Please select at least one service.');
            return;
        }
        console.log('Buy button clicked. KES Total:', total);
        
        $('#form-message').hide().removeClass('error success').text('');
        // NOTE: If you use the KES total in the modal content, update it here.
        $('#pay-modal').show();
    });

    // 2. Modal Closing Logic (no change)
    $('#close-modal').on('click', function(){ 
        console.log('Closing modal.');
        $('#pay-modal').hide(); 
    });

    // 3. M-Pesa Confirmation Logic (Updated to use KES total from data attribute)
    $('#confirm-pay').on('click', function(){
        console.log('Confirm pay button clicked. Starting validation.');
        
        const phone = $('#phone').val().trim();
        const email = $('#pay-email').val().trim(); 
        const account = $('#account').val().trim();

        // Retrieve the total KES amount from the button's data attribute
        const totalKES = $('#buy-btn').data('amount-kes');
        
        // Data Collection (Now including type and name for the comprehensive payments.php logic)
        const selected = [];
        $('.pdf-checkbox:checked').each(function(){
            selected.push({
                name: $(this).data('name'), // Assuming you added data-name attribute
                type: $(this).data('type'), // Assuming you added data-type attribute (pdf or consult)
                ref: $(this).data('ref'),
                price: $(this).data('price') // Sending USD price
            });
        });

        // --- VALIDATION CHECKS START HERE ---
        if (!phone || !/^[0]\d{8,12}$/.test(phone)) {
            console.warn('Validation FAILED: Phone number invalid.');
            $('#form-message').addClass('error').show().text('Please enter a valid phone starting with 0 (e.g. 0720123456).');
            return; 
        }
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
            console.warn('Validation FAILED: Email invalid.');
            $('#form-message').addClass('error').show().text('Please enter a valid email.');
            return;
        }
        if (totalKES <= 0) { // Safety check
            console.warn('Validation FAILED: Total KES is zero.');
            $('#form-message').addClass('error').show().text('Please select services before confirming payment.');
            return;
        }
        // --- VALIDATION CHECKS END HERE ---

        console.log(`Validation PASSED. Sending KES ${totalKES} to payments.php.`);

        // disable button
        $('#confirm-pay').prop('disabled', true).text('Processing...');

        $.ajax({
            url: 'payments.php',
            method: 'POST',
            dataType: 'json',
            data: {
                phone: phone,
                email: email,
                account: account,
                // IMPORTANT: Send the KES total to payments.php
                amount: totalKES, 
                // Send the full selected service array (required by new payments.php logic)
                services: JSON.stringify(selected) 
            }
        }).done(function(resp){
            // ... (Rest of your existing success/failure logic) ...
            
            // Your existing logic relies on:
            // if (resp && resp.status === 'success') { ... } 
            
            // Re-enabling logic moved to .always()
            
        }) .fail(function(xhr, err, thrown){
            // ... (Rest of your existing failure logic) ...
            
        }).always(function(){
            console.log('AJAX Complete. Re-enabling button.');
            $('#confirm-pay').prop('disabled', false).text('Send Payment Prompt');
        });
    });
});
</script>
<!--
<script>
$(function(){
    // Log initialization status
    console.log('Document Ready: jQuery is initialized.');

    function updateTotal(){
        let total = 0;
        $('.pdf-checkbox:checked').each(function(){
            total += parseInt($(this).data('price')) || 0;
        });
        $('#total-amount').text('KES ' + total);
        return total;
    }

    $('.pdf-checkbox').on('change', updateTotal);

    $('#buy-btn').on('click', function(){
        const total = updateTotal();
        if (total <= 0) {
            alert('Please select at least one service.');
            return;
        }
        console.log('Buy button clicked. Opening modal.');
        // open modal
        $('#form-message').hide().removeClass('error success').text('');
        $('#pay-modal').show();
    });

    $('#close-modal').on('click', function(){ 
        console.log('Closing modal.');
        $('#pay-modal').hide(); 
    });

    // Final check for button attachment
    if ($('#confirm-pay').length === 0) {
        console.error('CRITICAL ERROR: #confirm-pay button was not found in the DOM.');
    } else {
        console.log('SUCCESS: #confirm-pay button found and handler attached.');
    }

    $('#confirm-pay').on('click', function(){
        console.log('Confirm pay button clicked. Starting validation.'); // THIS IS YOUR KEY LOG
        
        const phone = $('#phone').val().trim();
        // CRITICAL FIX: Use the corrected ID #pay-email
        const email = $('#pay-email').val().trim(); 
        const account = $('#account').val().trim();

        console.log('Input values - Phone:', phone, 'Email:', email); // Log Input Values

        // Data Collection for AJAX
        const selected = [];
        $('.pdf-checkbox:checked').each(function(){
            selected.push({
                filename: $(this).data('filename'),
                ref: $(this).data('ref'),
                price: $(this).data('price')
            });
        });
        
        // --- VALIDATION CHECKS START HERE ---
        if (!phone || !/^[0]\d{8,12}$/.test(phone)) {
            console.warn('Validation FAILED: Phone number invalid.');
            $('#form-message').addClass('error').show().text('Please enter a valid phone starting with 0 (e.g. 0720123456).');
            return; 
        }
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
            console.warn('Validation FAILED: Email invalid.');
            $('#form-message').addClass('error').show().text('Please enter a valid email.');
            return;
        }
        // --- VALIDATION CHECKS END HERE ---

        console.log('Validation PASSED. Proceeding to AJAX call.'); // This should now appear

        const total = selected.reduce((s, x) => s + parseInt(x.price), 0);
        const files = selected.map(x => x.filename);
        const refs = selected.map(x => x.ref);

        // disable button to avoid double clicks
        $('#confirm-pay').prop('disabled', true).text('Processing...');

        $.ajax({
            url: 'payments.php',
            method: 'POST',
            dataType: 'json',
            data: {
                phone: phone,
                email: email,
                account: account,
                amount: total,
                files: JSON.stringify(files),
                refs: JSON.stringify(refs)
            }
        }).done(function(resp){
      
    // LOG THE FULL RESPONSE OBJECT
    console.log('AJAX SUCCESS (Server returned a response). Full Response Object:', resp); 

if (resp && resp.status === 'success') {
// SUCCESS CASE:
const successMsg = 'Payment successful! Wait to download the file.';
        
        // 1. DISPLAY SUCCESS MESSAGE
        $('#form-message').removeClass('error').addClass('success').show().text(successMsg);
        
        // 2. CLEAR THE FORM INPUTS
        $('#phone, #pay-email, #account').val('');
        
        // 3. AUTOMATICALLY CLOSE THE MODAL AFTER A SHORT DELAY
        setTimeout(function() {
            $('#pay-modal').hide();
             if (resp.link) {
            window.location.href = resp.link;
             }
        }, 2000); // Closes the modal after 3 seconds so the user can read the success message
} else {
        // FAILURE CASE (e.g., payment was declined by the user)
        const msg = resp && resp.msg ? resp.msg : 'Payment failed. Server sent an unknown error.';
        $('#form-message').removeClass('success').addClass('error').show().text('Payment Failed: ' + msg);
    }
}) .fail(function(xhr, err, thrown){
    // Log the entire XHR object and error details
    console.error('AJAX FATAL ERROR DETAILS:');
    console.error('Status:', xhr.status); 
    console.error('Ready State:', xhr.readyState);
    console.error('Response Text (if any):', xhr.responseText.substring(0, 100)); // Log first 100 chars
    console.error('Error Type (from jQuery):', err);
    console.error('Thrown Error:', thrown);
    
    // Display the most useful information on the modal
    let errorMsg = 'Connection Reset: The server failed to respond. Check server logs.';

    if (xhr.status !== 0) {
        errorMsg = 'Server Error (' + xhr.status + '): ' + (xhr.responseText.substring(0, 50) || 'Unknown response.');
    }

    $('#form-message').removeClass('success').addClass('error').show().text(errorMsg);
    
}).always(function(){
    
console.log('AJAX Complete. Re-enabling button.');
$('#confirm-pay').prop('disabled', false).text('Send Payment Prompt');
       });
    });
});
</script>
-->
<script src="script.js"></script>
</body>
</html>