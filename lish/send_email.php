<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

$response = array();

//Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

//Apply to message too
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    // Validate essential fields
    if (!$name || !$email || !$subject || !$message) {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all required fields.';
    } elseif (!$email) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email address.';
    } else {

        $mailContent = "From: $name\nEmail: $email\n\nMessage:\n$message";
        $subject = "Website Inquiry";
        $attachments = null; 
        
//Send to the business, replyto $email
$mail_sent = sendEmail($businessEmail, $business, $subject, $mailContent, $attachments,$email);
    
        if ($mail_sent) {
            $response['status'] = 'success';
            $response['message'] = 'Your message has been sent successfully!';
        } else {
            // Log the error for debugging and return a generic user message
            error_log("Email failed to send from: $businessEmail. Subject: $subject");
            $response['status'] = 'error';
        $response['message'] = 'Failed to send the message. Please try again or use the phone/WhatsApp link.';
        }
        
    }
} else {
    
    // Handle non-POST requests
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
    
}

echo json_encode($response);
?>
