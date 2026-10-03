<?php
require '../config.php';
require '../database.php';
require_once dirname(__DIR__, 2) . '/hawlastke.php';


if ($_SERVER["REQUEST_METHOD"] <> "POST")  die("You can only reach this page by posting from the html form");
	
	
	//$req_dump = print_r($_REQUEST, TRUE);
	//$fp = fopen("BUYNEWpost.txt", "a") or die("Unable to open file!");
	//fwrite($fp, $req_dump);
	//fclose($fp);
	
// THE AIRTIME BALANCE FROM AT
// Loaded through the central library loader (Composer vendor folder)
require_once dirname(__DIR__, 2) . '/libraries.php';

use AfricasTalking\SDK\AfricasTalking;

//constants should not have '' or ""
$username = ATUSER;
$apiKey   = ATAPIKEY; 
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
$TransID = $_POST['TransID']; 
$currencyCode = 'KES';

if(empty($phone)) {
    echo "Phone number is not provided";
    exit();
}

if(empty($amount)) {
    echo "Amount is not provided";
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

/*
if ($apiBalance < $amount) {

$from = "HAWLAST";
// sms message here
$message = "Your airtime of KES $amount for $phone will be topped up ASAP. Hawlast.com.";

$ceo = "+254720401869";
$recipients = implode(',', array_filter([$ceo, $phone]));

// Get the SMS service
$sms = $AT->sms();

// Set your shortCode or senderId
$from       = "HAWLAST";

try {
    // Thats it, hit send and we'll take care of the rest
    $result = $sms->send([
        'to'      => $recipients,
        'message' => $message,
        'from'    => $from
    ]);

   //print_r($result);
} catch (Exception $e) {
    echo "Error: ".$e->getMessage();
}

exit();
} 
*/

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Prepare and execute the query
    $query = $pdo->prepare("SELECT date, phone, TransID FROM airtime_transactions WHERE TransID = ? and phone =?");
    $query->execute([$TransID,$phone]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
     echo "This is a duplicate transaction.";
     exit();
        }

} catch (PDOException $e) {
    // Handle database errors
    echo "Error in checking duplicate: ".$e->getMessage()."\n";
    exit();
    
} finally {
    // Close the database connection
    Database::disconnect();
}

// Send the airtime after the various checks 
try {
$results = $airtime->send($parameters, $options);

//print_r($results);

   $overallErrorMessage = $results['data']->errorMessage;
    $numSent = $results['data']->numSent;
    $totalAmount = $results['data']->totalAmount;
    $totalDiscount = $results['data']->totalDiscount;


        $responseStatus = $results['data']->responses[0]->status;
        $amount = $results['data']->responses[0]->amount;
        $amount = kes($amount); //Strip the KES and float
        $phone = $results['data']->responses[0]->phoneNumber;
        $discount = $results['data']->responses[0]->discount;
        $requestId = $results['data']->responses[0]->requestId;
        $errorMessage = $results['data']->responses[0]->errorMessage; 

//can be sent / failed 
//Updgrade: send SMS the client and admin on failed status

echo $responseStatus . "<br>";
echo "Error Message: " . $errorMessage . "<br>";

$userid = "10"; 
$date = date("Y-m-d H:i:s");

//INSERT DATA INTO DATABASE
$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "INSERT INTO airtime_transactions (airtimeid,date,phone,amount,discount,status,requestid,TransID) values(?,?,?,?,?,?,?,?)";
$q = $pdo->prepare($sql);
$q->execute(array($userid,$date,$phone,$amount,$discount,$responseStatus,$requestId,$TransID));
Database::disconnect();

//If successful show us the updated results
$path = dirname($_SERVER['REQUEST_URI']);
$final .= 'https://'.$_SERVER['HTTP_HOST'].$path.'/';
$id = '?id=';
header("Location: $final$id$requestId");

} catch (Exception $e) {
    echo "Error sending airtime: " . $e->getMessage();
}
 
?>