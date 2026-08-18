<?php 
date_default_timezone_set("Africa/Nairobi");
require '../hawlastke.php';
require 'database.php';
 
if (!empty($_POST)) {

	  // $req_dump = print_r($_REQUEST, TRUE);
	 // $fp = fopen("airtimepost.txt", "a") or die("Unable to open file!");
 // fwrite($fp, $req_dump);
 	// fclose($fp);

 // keep track validation errors
$statusError = null;
        $requestidError = null;
         
        // keep track post values
        $status= $_POST['status'];
        $requestid = $_POST['requestId'];
         
        // validate input
        $valid = true;

        if (empty($status)) {
            $statusError = 'Please enter status';
            $valid = false;
        }
           
        if (empty($requestid)) {
            $requestidError = 'Please enter request id';
            $valid = false;
        }
         
        
//CHECK IF status is Success and reduce the balance
if ($valid) {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Update status immediately (using prepared statement for security)
    $sql = "UPDATE airtime_transactions SET status = ? WHERE requestid = ?";
    $pdo->prepare($sql)->execute([$status, $requestid]);

    // 2. Fetch transaction details once
    $stmt = $pdo->prepare("SELECT airtimeid, amount, phone, TransID FROM airtime_transactions WHERE requestid = ?");
    $stmt->execute([$requestid]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    if ($row) {
        if ($status === "Success") {
            // Deduct balance
            $upd = $pdo->prepare("UPDATE sms_users SET bal = bal - ? WHERE airtimeid = ?");
            $upd->execute([$row->amount, $row->airtimeid]);

            // Success Notification
            if ($row->amount > 149) {
                $msg = "Success! Ksh {$row->amount} airtime sent. We truly value your business. Thanks for choosing Hawlast Ventures! Support: 0720401869";
                TumaSMS($msg, $row->phone);
            }
        } elseif ($status === "Failed") {
            // Failure Notification (User + Admin)
            $message = "Sorry! Ksh {$row->amount} airtime failed (Ref: {$row->TransID}). Refund to MPESA soon. Try again later. Thanks, Hawlast Ventures 0720401869";
            $recipients = "+254720401869," . $row->phone;
            
            TumaSMS($message, $recipients);
        }
    }

    Database::disconnect();
}


}
//End of  POST
?>