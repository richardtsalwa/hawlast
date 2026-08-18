<?php
date_default_timezone_set('Africa/Nairobi');
function mlog($script, $reqId, $msg) {
    $line = sprintf(
        "[%s.%03d] [PID:%d] [%s] [%s] %s\n",
        date('Y-m-d H:i:s'),
        (microtime(true) - floor(microtime(true))) * 1000,
        getmypid(),
        $script,
        $reqId,
        $msg
    );
    $fp = fopen(__DIR__ . '/mpesa-debug-new.log', 'a');
    if ($fp) {
        flock($fp, LOCK_EX);
        fwrite($fp, $line);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
$reqId = substr(md5(uniqid('', true)), 0, 8);

ini_set('zlib.output_compression', 'Off');
ini_set('output_buffering', 'Off');

require '../config.php';
require '../hawlastke.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ResultCode" => 1, "ResultDesc" => "Invalid Request Method."]);
    exit;
}

$raw_callback = file_get_contents('php://input');
$fp = fopen("stkCallback.txt", "a");
if ($fp) { fwrite($fp, "--- [" . date('Y-m-d H:i:s') . "] ---\n$raw_callback\n\n"); fclose($fp); }

$data = json_decode($raw_callback, true);
if (!isset($data['Body']['stkCallback'])) {
    echo json_encode(["ResultCode" => 1, "ResultDesc" => "Invalid Payload Format"]);
    exit;
}


mlog('STKCB', $reqId, "=== START raw payload received ===");

// --- ACK Safaricom immediately ---
$response = json_encode(["ResultCode" => 0, "ResultDesc" => "Callback processed successfully"]);
ignore_user_abort(true);
header('Content-Length: ' . strlen($response));
header('Connection: close');
echo $response;
while (ob_get_level() > 0) { ob_end_flush(); }
flush();

// --- Process after client disconnected ---
$callback          = $data['Body']['stkCallback'];
$checkoutRequestID = trim($callback['CheckoutRequestID'] ?? '');
$resultCode        = (int)($callback['ResultCode'] ?? -1);
$resultDesc        = $callback['ResultDesc'] ?? '';

$amount = 0; $mpesaReceiptNumber = ''; $transactionDate = ''; $phoneNumber = '';
if (isset($callback['CallbackMetadata']['Item'])) {
    foreach ($callback['CallbackMetadata']['Item'] as $item) {
        switch ($item['Name']) {
            case 'Amount': $amount = $item['Value']; break;
            case 'MpesaReceiptNumber': $mpesaReceiptNumber = $item['Value']; break;
            case 'TransactionDate': $transactionDate = $item['Value']; break;
            case 'PhoneNumber': $phoneNumber = $item['Value']; break;
        }
    }
}
$transTimeFormatted = $transactionDate ? (string)$transactionDate : null;
mlog('STKCB', $reqId, "Parsed: checkoutRequestID=[$checkoutRequestID] resultCode=[$resultCode] receipt=[$mpesaReceiptNumber]");

$peek = $pdo->prepare("SELECT BillRefNumber FROM mpesaapi WHERE CheckoutRequestID = ?");
$peek->execute([$checkoutRequestID]);
$peekRow = $peek->fetch(PDO::FETCH_ASSOC);

if (!$peekRow) {
    mlog('STKCB', $reqId, "ABORT: no row found for CheckoutRequestID $checkoutRequestID");
    exit;
}
mlog('STKCB', $reqId, "Peeked BillRefNumber=[{$peekRow['BillRefNumber']}]");

$lockKey = 'mpesa_txn_' . $peekRow['BillRefNumber'];
mlog('STKCB', $reqId, "Requesting GET_LOCK($lockKey)...");
$lockStmt = $pdo->prepare("SELECT GET_LOCK(:key, 15) AS got_lock");
$lockStmt->execute([':key' => $lockKey]);
$gotLock = $lockStmt->fetch(PDO::FETCH_ASSOC)['got_lock'];
mlog('STKCB', $reqId, "GET_LOCK result=[$gotLock]");

if ($gotLock != 1) {
    mlog('STKCB', $reqId, "ABORT: could not acquire mutex for $lockKey");
    exit;
}

try {
    $pdo->beginTransaction();
    mlog('STKCB', $reqId, "Transaction started. Selecting row FOR UPDATE...");

    $lock = $pdo->prepare("SELECT * FROM mpesaapi WHERE CheckoutRequestID = ? FOR UPDATE");
    $lock->execute([$checkoutRequestID]);
    $row = $lock->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        mlog('STKCB', $reqId, "Row vanished. Committing empty.");
        $pdo->commit();
        exit;
    }
    mlog('STKCB', $reqId, "Row Auto={$row['Auto']} current done={$row['done']}");

    if ($row['done'] != 0) {
        mlog('STKCB', $reqId, "SKIP: Auto={$row['Auto']} already done={$row['done']}");
        $pdo->commit();
        exit;
    }

    if ($resultCode === 0) {
        $dupe = $pdo->prepare("SELECT Auto FROM mpesaapi WHERE TransID = ? AND Auto != ?");
        $dupe->execute([$mpesaReceiptNumber, $row['Auto']]);
        $existingDupe = $dupe->fetch(PDO::FETCH_ASSOC);
        mlog('STKCB', $reqId, "Dupe check for TransID=[$mpesaReceiptNumber]: " . ($existingDupe ? "FOUND Auto={$existingDupe['Auto']}" : "none"));

        if ($existingDupe) {
            mlog('STKCB', $reqId, "MERGING C2B Auto={$existingDupe['Auto']} into Auto={$row['Auto']}");

            $c2b = $pdo->prepare("SELECT * FROM mpesaapi WHERE Auto = ? FOR UPDATE");
            $c2b->execute([$existingDupe['Auto']]);
            $c2bRow = $c2b->fetch(PDO::FETCH_ASSOC);

            $merge = $pdo->prepare("
                UPDATE mpesaapi SET
                    done = 1, ResultCode = :res_code, ResultDesc = :res_desc,
                    TransID = :trans_id, TransAmount = :amount, MSISDN = :phone,
                    TransTime = :trans_time, TransactionType = :ttype,
                    BusinessShortCode = :bsc, FirstName = :fname,
                    MiddleName = :mname, LastName = :lname,
                    OrgAccountBalance = :bal, confirmation_status = 1
                WHERE Auto = :auto
            ");
            $merge->execute([
                ':res_code' => $resultCode, ':res_desc' => $resultDesc,
                ':trans_id' => $mpesaReceiptNumber, ':amount' => $amount,
                ':phone' => $phoneNumber, ':trans_time' => $transTimeFormatted,
                ':ttype' => $c2bRow['TransactionType'], ':bsc' => $c2bRow['BusinessShortCode'],
                ':fname' => $c2bRow['FirstName'], ':mname' => $c2bRow['MiddleName'],
                ':lname' => $c2bRow['LastName'], ':bal' => $c2bRow['OrgAccountBalance'],
                ':auto' => $row['Auto']
            ]);

            $del = $pdo->prepare("DELETE FROM mpesaapi WHERE Auto = ?");
            $del->execute([$existingDupe['Auto']]);

            mlog('STKCB', $reqId, "MERGE DONE. Deleted Auto={$existingDupe['Auto']}, merge rowCount=" . $merge->rowCount());

        } else {
            mlog('STKCB', $reqId, "Plain UPDATE on Auto={$row['Auto']} with TransID=[$mpesaReceiptNumber]");

            $stmt = $pdo->prepare("
                UPDATE mpesaapi SET
                    done = 1, ResultCode = :res_code, ResultDesc = :res_desc,
                    TransID = :trans_id, TransAmount = :amount,
                    MSISDN = :phone, TransTime = :trans_time
                WHERE Auto = :auto
            ");
            $stmt->execute([
                ':res_code' => $resultCode, ':res_desc' => $resultDesc,
                ':trans_id' => $mpesaReceiptNumber, ':amount' => $amount,
                ':phone' => $phoneNumber, ':trans_time' => $transTimeFormatted,
                ':auto' => $row['Auto']
            ]);

            mlog('STKCB', $reqId, "UPDATE DONE. rowCount=" . $stmt->rowCount());
        }

    } else {
        mlog('STKCB', $reqId, "Marking Auto={$row['Auto']} as FAILED (resultCode=$resultCode)");

        $stmt = $pdo->prepare("
            UPDATE mpesaapi SET done = 3, ResultCode = :res_code, ResultDesc = :res_desc
            WHERE Auto = :auto
        ");
        $stmt->execute([':res_code' => $resultCode, ':res_desc' => $resultDesc, ':auto' => $row['Auto']]);

        mlog('STKCB', $reqId, "FAIL UPDATE DONE. rowCount=" . $stmt->rowCount());
    }

    $pdo->commit();
    mlog('STKCB', $reqId, "COMMITTED.");

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    mlog('STKCB', $reqId, "EXCEPTION: " . $e->getMessage());
} finally {
    $pdo->prepare("SELECT RELEASE_LOCK(:key)")->execute([':key' => $lockKey]);
    mlog('STKCB', $reqId, "RELEASE_LOCK($lockKey) called. === END ===");
}
exit;