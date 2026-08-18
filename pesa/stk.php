<?php
// ========== CUSTOM ERROR LOGGING ==========
ini_set('display_errors', 0);                // Do not show errors to browser
error_reporting(E_ALL);                      // Log everything
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

// Shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        file_put_contents(__DIR__ . '/php-error.log', 
            date('Y-m-d H:i:s') . ' FATAL: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'] . "\n", 
            FILE_APPEND
        );
    }
});

// Set a custom error handler to log all non-fatal errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    file_put_contents(__DIR__ . '/php-error.log', 
        date('Y-m-d H:i:s') . " ERROR [$errno] $errstr in $errfile on line $errline\n", 
        FILE_APPEND
    );
    return true;
});

// Enable output buffering to catch any unexpected output
ob_start();

date_default_timezone_set('Africa/Nairobi');
require '../config.php';
require '../hawlastke.php';

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");

$error ="";
#https://developer.safaricom.co.ke/lipa-na-m-pesa-online/apis/post/stkpush/v1/processrequest

//ENSURE THAT REQUESTS TO THIS SCRIPT ARE AUTHORISED
//EG APPEND A PREDEFINED TOKEN IN URL OR WHITE LIST CERTAIN DOMAINS

# credentials 
$Passkey ="9b2aa55af213959611bdae2c8d388bc5b8eca3c543444f157ed4bc0b4bcf48e2";

$consumerKey = 'cENa8B1cLgQZGc6cdjweNIPNyc6E7PlI'; 
//Fill with your app Consumer Key

$consumerSecret = 'bmLplh6wVuzbO6aR'; 
// Fill with your app Secret
  
$BusinessShortCode = '822490';

//Clients phone should an MPESA NUMBER in format 254
$PartyA = trim($_POST["phone"]);

$PartyA = add254stk($PartyA);

$AccountReference = trim($_POST["account"]);

//Amount must be interger and not greater than 250,000
$Amount = trim($_POST["amount"]);

// Cast to integer
$Amount = (int) $Amount;

$TransactionDesc = 'Deposit';

// Check if there's already a pending transaction for this phone and order
$stmt = $pdo->prepare("SELECT * FROM mpesaapi WHERE MSISDN = ? AND BillRefNumber = ? AND done = 0");
$stmt->execute([$PartyA, $AccountReference]);
if ($stmt->fetch()) {
    echo "FAILED: A payment is already in progress. Please wait for the previous attempt to complete.";
    exit;
}

# Get the timestamp, format YYYYmmddhms -> 20181004151020
$Timestamp = date('YmdHis');    

# Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
$Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);
#header for access token
$headers = ['Content-Type:application/json; charset=utf8'];

# M-PESA endpoint urls
$access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$initiate_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

  # callback url
  $CallBackURL = 'https://www.hawlast.com/pesa/stkcallback.php';  
  
  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);
  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
  $result = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  
 // check for execution errors
 if(curl_errno($curl)) {
 $error = curl_error($curl);
 $access_token ="TisISIT";} else {
  $result = json_decode($result);
  $access_token = $result->access_token; 
	}
 
  curl_close($curl);
  # header for stk push
  $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
  # initiating the transaction
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $initiate_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header
  $curl_post_data = array(
    //Fill in the request parameters with valid values
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $PartyA,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
  );
  $data_string = json_encode($curl_post_data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  $curl_response = curl_exec($curl);
  //print_r($curl_response);
  //echo $curl_response;

$results = null;
$message = null;

if (!empty($curl_response)) {
    $results = json_decode($curl_response, true);
    
    // --- API Request Error (Bad phone, bad amount, etc.) ---
    if (isset($results['errorMessage'])) {
        $rawError = $results['errorMessage'];

        if ($rawError == 'Bad Request - Invalid Amount') {
            $err = 'The amount allowed by M-PESA is between Ksh. 1 and Ksh. 250,000.';
        } elseif ($rawError == '[STK] - Unable to lock subscriber, a transaction is already in process for the current subscriber') {
            $err = 'We are still processing a recent transaction on this M-PESA number. Please wait a moment before retrying.';
        } elseif ($rawError == '[CBS - ] No ICCID found') {
            $err = 'Wrong service provider. Phone number must be an active Safaricom M-PESA number.';
        } elseif ($rawError == 'Service is currently under maintenance. Please try again later') {
            $err = 'M-PESA is currently undergoing maintenance. Please try again later.';
        } elseif ($rawError == 'Bad Request - Invalid PhoneNumber') {
            $err = 'Invalid phone number format. Must start with 2547xxxx or 2541xxxx.';
        } else {
            $err = $rawError;
        }

        $message .= "API Error: " . $err;
        echo "FAILED: " . $err;

    // --- STK Push Successfully Sent to Phone ---
    } elseif (isset($results['ResponseCode']) && $results['ResponseCode'] == '0') {
        
        $checkoutRequestID = trim($results['CheckoutRequestID'] ?? '');
        $merchantRequestID = trim($results['MerchantRequestID'] ?? '');

error_log("STK Debug: checkoutRequestID = [$checkoutRequestID], merchantRequestID = [$merchantRequestID]");

// Generate a unique temporary TransID (10 chars)
function generateTempTransID($pdo) {
    $prefix = 'HV';
    $maxAttempts = 60;
    $attempt = 0;
    
    while ($attempt < $maxAttempts) {
        $random = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        $candidate = $prefix . $random;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM mpesaapi WHERE TransID = ?");
        $stmt->execute([$candidate]);
        if ($stmt->fetchColumn() == 0) {
            return $candidate;
        }
        $attempt++;
    }
    // Ultimate fallback: use hash of microtime
    return $prefix . substr(md5(uniqid(mt_rand(), true)), 0, 8);
}

$tempTransID = generateTempTransID($pdo);


$lockKey = 'mpesa_txn_' . $AccountReference;
$pdo->prepare("SELECT GET_LOCK(:key, 10) AS l")->execute([':key' => $lockKey]);

$pdo->beginTransaction();

// Has C2B already delivered this payment before our STK insert even ran?
$find = $pdo->prepare("
    SELECT Auto FROM mpesaapi
    WHERE BillRefNumber = :ref
      AND TransAmount = :amount
      AND CheckoutRequestID IS NULL
      AND confirmation_status = 1
    ORDER BY Auto DESC LIMIT 1
    FOR UPDATE
");
$find->execute([':ref' => $AccountReference, ':amount' => $Amount]);
$earlyC2B = $find->fetch(PDO::FETCH_ASSOC);

if ($earlyC2B) {
    // Attach this STK request's IDs to the row C2B already created — no stub needed
    $pdo->prepare("UPDATE mpesaapi SET CheckoutRequestID = ?, MerchantRequestID = ? WHERE Auto = ?")
        ->execute([$checkoutRequestID, $merchantRequestID, $earlyC2B['Auto']]);
} else {
    // Normal path: no C2B row yet, insert the temp stub as before
    $stmt = $pdo->prepare("INSERT INTO mpesaapi (BillRefNumber, TransAmount, MSISDN, CheckoutRequestID, MerchantRequestID, TransID, done) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->execute([$AccountReference, $Amount, $PartyA, $checkoutRequestID, $merchantRequestID, $tempTransID]);
}

$pdo->commit();
$pdo->prepare("SELECT RELEASE_LOCK(?)")->execute([$lockKey]);

// Continue with polling...
$message .= "STK Sent. Waiting for user input... ";

// 2. Poll DB every 2s (up to 60s) for stkcallback.php to update this row
 $max_attempts = 60; // 60 * 2s = 120s
$attempt = 0;

while ($attempt < $max_attempts) {
    sleep(2);
    $attempt++;

    $stmt = $pdo->prepare("SELECT * FROM mpesaapi WHERE CheckoutRequestID = ? ORDER BY Auto DESC LIMIT 1");
    $stmt->execute([$checkoutRequestID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        if ($row['done'] == 1 || (isset($row['ResultCode']) && $row['ResultCode'] == 0)) {
            // Success
            $update = $pdo->prepare("UPDATE mpesaapi SET done = 2 WHERE Auto = ?");
            $update->execute([$row['Auto']]);
            echo "Success";
            exit;
        } elseif ($row['done'] == 3 || (isset($row['ResultCode']) && $row['ResultCode'] != 0)) {
            // Failure – map error codes
            $code = (int)$row['ResultCode'];
            $error_desc = match ($code) {
                1    => "Insufficient M-PESA balance.",
                1032 => "You cancelled the payment prompt.",
                1037 => "Could not reach your phone.",
                2001 => "Incorrect PIN entered.",
                default => $row['ResultDesc'] ?? "Transaction failed."
            };
            echo "FAILED: " . $error_desc;
            exit;
        }
        // If row exists but still pending (done=0), continue polling
    }
}
// Timeout
echo "FAILED: Payment prompt timed out. Please check your phone and try again.";
exit;

    } else {
        $err = $results['ResponseDescription'] ?? 'Unable to initiate STK push prompt.';
        $message .= "Response error: " . $err;
        echo "FAILED: " . $err;
    }} else {
    echo "FAILED: Empty response from M-PESA gateway.";
}

// Log execution output
$message .= "\nRequest: " . print_r($_REQUEST, true) . "\n";
$fp = fopen("stk-push.txt", "a");
if ($fp) {
    fwrite($fp, "[" . date('Y-m-d H:i:s') . "] " . $message . "\n--------------------------------\n");
    fclose($fp);
}
?>