<?php 
require '../config.php';
require '../hawlastke.php';
date_default_timezone_set('Africa/Nairobi');

if ($request = file_get_contents('php://input')) {
    $payload = file_get_contents('php://input');
    $array = json_decode($payload, true);

    $transamount = $array['TransAmount'];
    $billrefno   = $array['BillRefNumber'];

    // Airtime API does not accept less than 5.
    if ($transamount < 5) {
        $response = array('ResultCode' => 'C2B00013', 'ResultDesc' => 'Rejected: Transaction amount is less than the minimum allowed');
    } else {
        $response = array('ResultCode' => 0, 'ResultDesc' => 'Success');
    }

    $rejectedPrefixes = ['MD'];
    $startsWithRejected = false;
    foreach ($rejectedPrefixes as $prefix) {
        if (stripos($billrefno, $prefix) === 0) {
            $startsWithRejected = true;
            break;
        }
    }
    if ($startsWithRejected) {
        $response = array('ResultCode' => 'C2B00012', 'ResultDesc' => 'Rejected');
    }

    ob_start();
    header("Content-Type: application/json");
    echo json_encode($response);
    $size = ob_get_length();
    header("Content-Length: $size");
    header("Connection: close");
    ob_end_flush();
    flush();
    ignore_user_abort(true);

    // NOTE: No DB write here. Validation only accepts/rejects.
    // Actual persistence happens exclusively in confirmation.php,
    // which fires only after Safaricom finalizes the transaction.
}
?>