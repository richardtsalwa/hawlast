<?php
// === ENVIRONMENT SETUP ===
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/payment_error.log');
error_reporting(E_ALL);

// === DEBUG: TEST LOG PERMISSIONS IMMEDIATELY ===
error_log("--- PHP Script Execution Started: " . date('Y-m-d H:i:s') . " ---");

require_once 'config.php';
require_once 'functions.php';

// ADD THIS NEW LOG LINE HERE
error_log("--- Dependencies Loaded. Starting Data Processing. ---");


header('Content-Type: application/json');

function jsonErr($msg) { echo json_encode(['status'=>'error','msg'=>$msg]); exit; }
function jsonOk($msg, $extra=[]) { echo json_encode(array_merge(['status'=>'success','msg'=>$msg], $extra)); exit; }

// --- 1. Data Retrieval and Validation ---

$phone   = $_POST['phone']   ?? null;
$email   = $_POST['email']   ?? null;

if (!$phone || !preg_match('/^[0]\d{8,12}$/', $phone)) jsonErr('Invalid phone number');

//Create a unique reference
$account = 'LL' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
$account = str_replace('0', 'H', $account);

// Amount is the KES total
$amount  = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;

// NEW: Use null coalescing with default empty array string to fix json_decode deprecation warning
$services_json = $_POST['services'] ?? '[]'; 
$services = json_decode($services_json, true);

if (!$phone || !preg_match('/^[0]\d{8,12}$/', $_POST['phone'])) jsonErr('Invalid phone number (must start with 0).');
if ($amount <= 5) jsonErr('Amount must be greater than 5');
if ($amount > 250000) jsonErr('Amount exceeds the limit');
if (!is_array($services) || empty($services)) jsonErr('No services selected.');

// --- 2. Determine Purchase Type ---

$is_consultation = false;
$consultation_names = [];
$pdf_names = [];

foreach ($services as $item) {
    // Check if ANY item is a consultation
    if (isset($item['type']) && $item['type'] === 'consult') {
        $is_consultation = true;
        $consultation_names[] = $item['name'] ?? 'Consultation Service';
    } elseif (isset($item['type']) && $item['type'] === 'pdf') {
        $pdf_names[] = $item['name'] ?? 'Ebook Guide';
    }
}

// If one or more consultation services were bought, the action is consultation
$action_type = $is_consultation ? 'consultation' : 'download';


error_log("--- Starting DB Transaction. ---");

error_log("--- Starting DB Transaction. ---");
try {
    // THIS LINE IS WHERE THE ERROR IS OCCURRING: $pdo must be a valid connection
    $pdo->beginTransaction(); 
    
    $stmt = $pdo->prepare("INSERT INTO purchases (phone, email, account_ref, amount, status, data) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$phone, $email, $account, $amount, $services_json]);
    $purchase_id = $pdo->lastInsertId();
} catch (Exception $e) {
    // --- CRITICAL DEBUG LOG ADDED HERE ---
    error_log("FATAL DB ERROR: " . $e->getMessage());
    
    if (isset($pdo) && method_exists($pdo, 'inTransaction') && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // We stop execution and send an error message to the client
    jsonErr("DB insert error: " . $e->getMessage()); 
}

// --- 4. Send STK push via CURL (Using 254 format) ---
$hawalst_url = "https://www.hawlast.com/pesa/stk.php";
$payload = [
    "phone"   => $phone,
    "account" => $account,
    "amount"  => $amount
];

// --- DEBUG STEP 1: LOG THE PAYLOAD BEING SENT ---
error_log("STK Payload (ID: {$purchase_id}): " . print_r($payload, true));

$ch = curl_init($hawalst_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 90,
    CURLOPT_SSL_VERIFYPEER => true
]);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// --- DEBUG STEP 2: LOG THE RAW RESPONSE AND ANY CURL ERROR ---
error_log("STK Response (ID: {$purchase_id}): " . ($response ?: 'EMPTY RESPONSE'));
error_log("cURL Error (ID: {$purchase_id}): " . ($error ?: 'NONE'));

$hawalst_response = $response ?: ('CURL_ERROR: ' . $error);
$status = (stripos($response, 'Success') !== false) ? 'success' : 'failed';

try {
    $stmt = $pdo->prepare("UPDATE purchases SET hawalst_response=?, status=? WHERE id=?");
    $stmt->execute([$hawalst_response, $status, $purchase_id]);
    
    // We only commit the transaction if the STK push was successful
    if ($status === 'success') {
        // We will commit later inside the conditional block, or if consultation
    } else {
         $pdo->commit();
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    jsonErr("DB update error after STK push: " . $e->getMessage());
}

if ($status !== 'success') {
    jsonErr("Payment failed. Response: $hawalst_response");
}

// --- 5. Conditional Fulfillment Logic (Ebook vs Consultation) ---

$user_link = null;
$user_subject = null;
$user_email_body = null;
$fulfillment_message = "Payment successful. ";

if ($action_type === 'download') {
    // A. EBOOK (DOWNLOAD) FULFILLMENT
    
    // Create download token
    try {
        $token = bin2hex(random_bytes(32));
        $download_limit = 2;
        $expires_at = (new DateTime('+7 days'))->format('Y-m-d H:i:s');
        
        // Use the original services JSON string as the data to be downloaded
        $data = $services_json; 

        $stmt = $pdo->prepare("INSERT INTO download_tokens (purchase_id, token, data, download_limit, downloads, expires_at)
                               VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->execute([$purchase_id, $token, $data, $download_limit, $expires_at]);
        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        jsonErr("Token creation failed: " . $e->getMessage());
    }

    // Build download link
    $download_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                     "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['REQUEST_URI']) .
                     "/download.php?token={$token}";

    $user_link = $download_link;
    $fulfillment_message .= "Your download link has been successfully generated.";
    $user_subject = "Your Ebook Download Link";
    $user_email_body = "Thank you for purchasing the following eBook(s): " . implode(", ", $pdf_names) . ". You can download your file(s) using this link: <a href='{$download_link}'>Download Link</a>";

} else {
    // B. CONSULTATION FULFILLMENT

    // We use the booking link assumed to be in config.php
    // !!! ASSUMPTION: $calendarBookingLink is defined in config.php !!!
    $booking_link = $calendarBookingLink ?? "https://your-booking-link.com/default-calendar-link"; 
    
    // Commit the transaction now that we know the STK push was successful
    $pdo->commit(); 

    $user_link = $booking_link;
    $fulfillment_message .= "Check your email for the link to book your consultation!";
    $user_subject = "Next Steps for Your Consultation";
    $user_email_body = "Thank you for purchasing the consultation service(s): " . implode(", ", $consultation_names) . ". Please use the following link to book your preferred time on my calendar: <a href='{$booking_link}'>Book Your Consultation</a>";
}

// --- 6. Admin Email Notification ---

$subject = "New Payment Received - " . ucfirst($action_type);
$items_list = "<ul>";
foreach ($services as $item) {
    // Assuming you have $currencyUSD defined in config.php
    $items_list .= "<li>{$item['name']} ({$item['type']}) - {$currencyUSD}" . ($item['price'] ?? 0) . "</li>";
}
$items_list .= "</ul>";

$time = date('Y-m-d H:i:s');

$mailContent = "
    <p><strong>New Payment Notification</strong></p>
    <p><b>Phone:</b> 254{$phone}</p>
    <p><b>Email:</b> {$email}</p>
    <p><b>Amount:</b> KES {$amount}</p>
    <p><b>Time:</b> {$time}</p>
    <p><b>Items Purchased:</b></p>{$items_list}
    <p><b>Fulfillment Type:</b> " . ucfirst($action_type) . "</p>
    <p><b>Link:</b> <a href='{$user_link}' target='_blank'>{$user_link}</a></p>
    <p><b>Do not click on the link. You can send to the user if they did not receive the fulfillment email.</b></p>
    <hr>
    <p>Sent automatically by {$companyName} system.</p>
";

$attachments = [];

// Send email to the Admin (businessEmail)
$sent_admin = sendEmail($businessEmail, $business, $subject, $mailContent, $attachments, $email);

if (!$sent_admin) {
    error_log("Admin email failed for payment ID {$purchase_id}");
}


// --- 7. Client Fulfillment Email (New Step) ---

// Send email to the user (if email provided)
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Send email using the user's email address as the recipient
    $sent_user = sendEmail($email, $business, $user_subject, $user_email_body, $attachments, $businessEmail);
    if (!$sent_user) {
        error_log("User fulfillment email failed for payment ID {$purchase_id}");
    }
}


// --- 8. Return JSON to Client ---

// The front-end JS can use the 'link' for redirection for both types.
jsonOk($fulfillment_message, ['link' => $user_link]);