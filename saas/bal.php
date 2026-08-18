<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Be sure to include the file you've just downloaded
require_once('AfricasTalkingGateway.php');

// Specify your login credentials
$username = "hawlast";
$apiKey   = "d7b6173c4bd4f1396432cf94cb934eadd08716cd1df075f562cdd0456df423f8"; 


// Create a new instance of our awesome gateway class
$gateway    = new AfricasTalkingGateway($username, $apikey);
// Any gateway errors will be captured by our custom Exception class below, 
// so wrap the call in a try-catch block
try
{ 
  // Fetch the data from our USER resource and read the balance
  $data = $gateway->getUserData();
  echo "Balance: " . $data->balance."\n";
  // The result will have the format=> KES XXX
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while fetching user data: ".$e->getMessage()."\n";
}
