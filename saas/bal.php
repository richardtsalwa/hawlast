<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Be sure to include the library loader
require_once dirname(__DIR__) . '/libraries.php';

// Specify your login credentials


// Create a new instance of our awesome gateway class
$AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);
// Any gateway errors will be captured by our custom Exception class below, 
// so wrap the call in a try-catch block
try
{ 
  // Fetch the data from our USER resource and read the balance
  $appData = $AT->application()->fetchApplicationData();
  echo "Balance: " . $appData['data']->UserData->balance."\n";
  // The result will have the format=> KES XXX
}
catch ( Throwable $e )
{
  echo "Encountered an error while fetching user data: ".$e->getMessage()."\n";
}
