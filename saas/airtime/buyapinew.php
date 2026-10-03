<?php
require '../config.php';
require '../database.php';
require_once dirname(__DIR__, 2) . '/hawlastke.php';

if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");
	
$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen("BUYNEWpost.txt", "a") or die("Unable to open file!");
fwrite($fp, $req_dump);
fclose($fp);
	
// THE AIRTIME BALANCE FROM AT
// Loaded through the central library loader (Composer vendor folder)
require_once dirname(__DIR__, 2) . '/libraries.php';

use AfricasTalking\SDK\AfricasTalking;

//constants should not have '' or ""
$username = ATUSER;
$apiKey   = ATPASSWORD; 
$AT       = new AfricasTalking($username, $apiKey);

// Get the application service
$application = $AT->application();

try {
    // Fetch the application data
    $data = $application->fetchApplicationData();

    //print_r($data);
$apiBalance = $data['data']->UserData->balance;
$apiBalance = kes($apiBalance); //strip the KES from amount
 
} catch(Exception $e) {
    echo "Error: ".$e->getMessage();
}

//$apiBalance = "1200";

$airtime = $AT->airtime();

// Generate a unique idempotency key
// Generate a salt based on the current time in microseconds
$salt = microtime(true);

// Combine the salt with uniqid() to enhance uniqueness
$idempotencyKey  = substr(hash('sha256', uniqid($salt, true)), 0, 16);


// Define the recipient's phone number, currency, and amount
//phone must be in country code format

$amount = protect($_POST['amount']); 
$amount = safe($amount);
$phone = protect($_POST['phone']);  
$phone = safe($phone);
$TransID = $_POST['TransID']; //use this to ensure mpesa trans is used once
$currencyCode = 'KES';

if(empty($phone)) {
    echo "Phone number is not provided";
    error_log("Phone number is not provided.");
     
    exit();
}

if(empty($amount)) {
   //echo "Amount is not provided";
 error_log("Amount is not provided.");
      
    exit();
}

// Set up the parameters for sending airtime
$parameters = [
    'recipients' => [
        [
            'phoneNumber' => $phone,  // REQUIRED: Recipient phone number
            'currencyCode' => $currencyCode, // REQUIRED: Currency code
            'amount' => $amount // REQUIRED: Amount to send
        ]
    ]
];

// Optional: Define additional options for the request
$options = [
    'idempotencyKey' => $idempotencyKey, // Ensure idempotent requests
    'maxNumRetry' => 3 // Retry a maximum of 3 times if delivery fails
];

// DEBUG: Check values before the IF statement
error_log("Debug: API Balance is [$apiBalance], Required Amount is [$amount]");

//  Initial Check: API Balance
if ($apiBalance < $amount) {
    $message = "Your airtime of KES $amount for $phone will be topped up ASAP. Hawlast.com Support: 0720401869.";
    $ceo = "+254720401869";
    $recipients = implode(',', array_filter([$ceo, $phone]));
    
//error_log("Debug: Attempting TumaSMS to: $recipients with message: $message");
    
   // Call the function
    $status = TumaSMS($message, $recipients);
    
    // Log the result of the function if it returns anything
    error_log("Debug: TumaSMS status: " . print_r($status, true));
    
    exit(); // Stop here if balance is low
} else {
//error_log("Debug: Balance is sufficient. Proceeding to send airtime.");
}

// Database Operations Start
try {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- DUPLICATE CHECK USING SELECT 1 ---
$stmt = $pdo->prepare("SELECT 1 FROM airtime_transactions WHERE TransID = ? AND phone = ? LIMIT 1");
$stmt->execute([$TransID, $phone]);

// fetch() will return the value 1 if found, or false if not found
if ($stmt->fetch()) {
    error_log("Duplicate transaction detected: $TransID");
    Database::disconnect();
    exit("Error: This transaction has already been processed."); 
}
 // --- API CALL ---
    // Only call the API if the duplicate check passes
    try {
        
  // This frees the DB while you wait for the slow API
    Database::disconnect();
    
        $results = $airtime->send($parameters, $options);
        
        // Extract data safely
        $res = $results['data']->responses[0];
        $responseStatus = $res->status;
        $amountRaw      = $res->amount;
        $amount         = kes($amountRaw); // Ensure this function returns a numeric value
        $phone          = $res->phoneNumber;
        $discount       = $res->discount;
        $requestId      = $res->requestId;
        $errorMessage   = $res->errorMessage;

        // --- SMS LOGIC FOR FAILURE ---
        if ($responseStatus == "Failed") {
            $failMsg = "Sorry! Ksh $amount airtime failed. Expect a refund soon. Contact 0720401869.";
            TumaSMS($failMsg, "+254720401869," . $phone);
        }

        // --- INSERT DATA ---
        $userid = "10"; 
        $date = date("Y-m-d H:i:s");
        $pdo = Database::connect();
        $sql = "INSERT INTO airtime_transactions (airtimeid, date, phone, amount, discount, status, requestid, TransID) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $q = $pdo->prepare($sql);
        $q->execute([$userid, $date, $phone, $amount, $discount, $responseStatus, $requestId, $TransID]);
        
        echo "Success";

    } catch (Exception $apiEx) {
        error_log("API Error: " . $apiEx->getMessage());
        echo "API Connection Error: " . $apiEx->getMessage();
    }

} catch (PDOException $dbEx) {
    error_log("Database Error: " . $dbEx->getMessage());
    echo "Database Error: " . $dbEx->getMessage();
} finally {
    Database::disconnect();
}
?>