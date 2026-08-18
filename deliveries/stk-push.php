<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Authentication required.";
    exit();
}

include 'config.php';

require __DIR__ . '/../../vendor/autoload.php';

use Kopokopo\SDK\K2;

// Store your client ID, client secret, and API key as environment variables
$options = [
    'clientId' => 'YOUR_CLIENT_ID', 
    'clientSecret' => 'YOUR_CLIENT_SECRET',
    'apiKey' => 'YOUR_API_KEY',
    'baseUrl' => 'https://api.kopokopo.com' // Use the sandbox URL for testing
];

$K2 = new K2($options);

// Get the TokenService
$tokens = $K2->TokenService();

// Get the access token
$result = $tokens->getToken();

$accessToken = null;
if ($result['status'] === 'success') {
    $accessToken = $result['data']['accessToken'];
    echo "Access Token: " . $accessToken . "\n";
} else {
    echo "Error getting access token: " . $result['data']['errorMessage'] . "\n";
    exit;
}

// Ensure an access token was obtained
if ($accessToken) {
    // Get the StkService
    $stk = $K2->StkService();

    // Initiate the STK Push payment
    $result = $stk->initiateIncomingPayment([
        'paymentChannel' => 'M-PESA STK Push',
        'tillNumber' => 'K000000', // Replace with your actual till number
        'firstName' => 'John',
        'lastName' => 'Doe',
        'phoneNumber' => '0720401869', // Replace with the customer's phone number
        'amount' => 1, // Amount to request
        'email' => 'hawlast@gmail.com',
        'currency' => 'KES', // The only supported currency
        'callbackUrl' => 'https://www.hawlast.com/deliveries/stk-push.php', 
        // Replace with your webhook URL
        'accessToken' => $accessToken,
    ]);

    // Print the result
    if ($result['status'] === 'success') {
        echo "STK Push initiated successfully. Response:\n";
        print_r($result);
    } else {
        echo "Failed to initiate STK Push. Error:\n";
        print_r($result);
    }
}



// Specify the path to your log file
$logFile = 'kopokopo_callback_log.txt';

// Get the raw JSON data from the request body
$jsonData = file_get_contents('php://input');

// Check if there is data and decode it
if ($jsonData) {
    $data = json_decode($jsonData, true);

    // Format the data into a readable string
    $logEntry = "================== " . date('Y-m-d H:i:s') . " ==================\n";
    $logEntry .= print_r($data, true);
    $logEntry .= "========================================================\n\n";

    // Append the formatted data to the log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);

    // Respond to Kopokopo to acknowledge receipt of the webhook
    http_response_code(200);
    echo "Callback received and logged successfully.";

}

?>