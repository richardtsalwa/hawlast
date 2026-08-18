<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/payment_error.log');
error_reporting(E_ALL);

require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

function jsonErr($msg) { echo json_encode(['status'=>'error','msg'=>$msg]); exit; }
function jsonOk($msg, $extra=[]) { echo json_encode(array_merge(['status'=>'success','msg'=>$msg], $extra)); exit; }

$phone   = $_POST['phone']   ?? null;
$email   = $_POST['email']   ?? null; // User email (not used for notifications now)
//Create a unique reference
$account = 'LL' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
$account = str_replace('0', 'H', $account);

$amount  = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
//$files_json = $_POST['files'] ?? null;
$refs_json  = $_POST['refs'] ?? null;

if (!$phone || !preg_match('/^[0]\d{8,12}$/', $phone)) jsonErr('Invalid phone number');
if ($amount <= 5) jsonErr('Amount must be greater than 5');
if ($amount > 250000) jsonErr('Amount exceeds the limit');

$services_json = $_POST['services'] ?? '[]';

//$files = json_decode($files_json, true);
//if (!is_array($files) || empty($files)) jsonErr('No files selected');

// --- Create purchase record ---
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO purchases (phone, email, account_ref, amount, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$phone, $email, $account, $amount]);
    $purchase_id = $pdo->lastInsertId();
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    jsonErr("DB insert error: " . $e->getMessage());
}

// --- Send STK push via CURL ---
$hawalst_url = "https://www.hawlast.com/pesa/stk.php";
$payload = [
    "phone"   => $phone,
    "account" => $account,
    "amount"  => $amount
];

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

$hawalst_response = $response ?: ('CURL_ERROR: ' . $error);
$status = (stripos($response, 'Success') !== false) ? 'success' : 'failed';

try {
    $stmt = $pdo->prepare("UPDATE purchases SET hawalst_response=?, status=? WHERE id=?");
    $stmt->execute([$hawalst_response, $status, $purchase_id]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    jsonErr("DB update error: " . $e->getMessage());
}

if ($status !== 'success') {
    $pdo->commit();
    jsonErr("Payment failed. Response: $hawalst_response");
}

// --- Create download token ---
try {
    $token = bin2hex(random_bytes(32));
    $download_limit = 2;
    $expires_at = (new DateTime('+7 days'))->format('Y-m-d H:i:s');
    $data = json_encode($files, JSON_UNESCAPED_SLASHES);

    $stmt = $pdo->prepare("INSERT INTO download_tokens (purchase_id, token, data, download_limit, downloads, expires_at)
                           VALUES (?, ?, ?, ?, 0, ?)");
    $stmt->execute([$purchase_id, $token, $data, $download_limit, $expires_at]);
    $pdo->commit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    jsonErr("Token creation failed: " . $e->getMessage());
}

// --- Build download link ---
$download_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                 "://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['REQUEST_URI']) .
                 "/download.php?token={$token}";

// --- ADMIN email notification ---

$subject = "New Payment Received";

$time = date('Y-m-d H:i:s');

$mailContent = "
    <p><strong>New Payment Notification</strong></p>
    <p><b>Phone:</b> {$phone}</p>
    <p><b>Amount:</b> KES {$amount}</p>
    <p><b>Time:</b> {$time}</p>
    <p><b>Download Link:</b> <a href='{$download_link}' target='_blank'>{$download_link}</a></p>
<p><b>Do not click on the link. You can send to the user if for some reason they did not download the file.</b></p>
    
    <hr>
    <p>Sent automatically by {$companyName} system.</p>
";

$attachments = [];

$sent = sendEmail($businessEmail, $business, $subject, $mailContent, $attachments,$email);

// --- Return JSON so JS can redirect user to the download link ---
if (!$sent) {
    error_log("Admin email failed for payment ID {$purchase_id}");
}

jsonOk("Payment successful.", ['link' => $download_link]);
