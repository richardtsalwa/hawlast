<?php
$f = 'd:/xampp/htdocs/hawlast/saas/sms/api_sendsms.php';
$c = '<?php
/**
 * Android SMS API Endpoint
 * Receives SMS requests from Android app via POST
 * Parameters: message, phone, token (for security)
 * Returns: JSON response with success/failed status, timestamp, and cost
 */

header("Content-Type: application/json");

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../database.php";
require_once dirname(__DIR__, 2) . "/hawlastke.php";
require_once __DIR__ . "/../../libraries.php";

/**
 * Send JSON response and exit
 */
function jsonResponse(bool $success, string $message, array $extra = []): void {
    $response = [
        "success"   => $success,
        "message"   => $message,
        "timestamp" => date("Y-m-d H:i:s")
    ];
    if (!empty($extra)) {
        $response = array_merge($response, $extra);
    }
    echo json_encode($response);
    exit;
}

// Only POST allowed
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(false, "Only POST requests are allowed");
}

// Retrieve data (JSON or form-encoded)
$data = [];
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";

if (strpos($contentType, "application/json") !== false) {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true) ?? [];
} else {
    $data = $_REQUEST;
}

$message = isset($data["message"]) ? trim($data["message"]) : "";
$phone   = isset($data["phone"])   ? trim($data["phone"])   : "";
$token   = isset($data["token"])   ? trim($data["token"])   : "";

// Basic validation
if ($message === "" || $phone === "" || $token === "") {
    jsonResponse(false, "Missing required parameters: message, phone, or token");
}

if (strlen($message) > 160) {
    jsonResponse(false, "Message too long (maximum 160 characters for a single SMS)");
}

// Token Verification - Query database for token
$pdo = Database::connect();
$stmt = $pdo->prepare("SELECT airtimeid, bal FROM sms_users WHERE api_key = ? OR phone = ?");
$stmt->execute([$token, $token]);
$user = $stmt->fetch();

if (!$user) {
    Database::disconnect();
    jsonResponse(false, "Invalid token - authentication failed");
}

$userId = $user["airtimeid"];
$balance = (float) $user["bal"];

// Normalize and Validate Phone Number
$phoneNormalized = add254($phone);
if ($phoneNormalized === false) {
    Database::disconnect();
    jsonResponse(false, "Invalid phone number format");
}

// Handle multiple recipients (comma-separated)
$recipients = explode(",", $phoneNormalized);
$numberOfSMS = count($recipients);

// Check User Balance
$estimatedCost = $numberOfSMS * 0.50; // KES estimate per SMS

if ($balance < $estimatedCost) {
    Database::disconnect();
    jsonResponse(false, "Insufficient balance. Required: KES {$estimatedCost}, Available: KES {$balance}");
}

// Send SMS via the Africa\'s Talking SDK
try {
    $AT = new \AfricasTalking\SDK\AfricasTalking(ATUSER, ATAPIKEY);
    $from = "HAWLAST";

    $response = $AT->sms()->send([\'to\' => implode(",", $recipients), \'message\' => $message, \'from\' => $from]);

    $totalCost = 0.0;
    $successfulRecipients = 0;
    $failedRecipients = [];
    $logData = [];

    $results = ($response[\'data\']->SMSMessageData->Recipients ?? []);


    foreach ($results as $result) {
        $cost = (float) $result->cost;
        $totalCost += $cost;

        if ($result->status === "Success") {
            $successfulRecipients++;
            $logData[] = sprintf(
                "%s\t%s\t%s\t%s\t%s\t%s\t%s",
                $result->number,
                $userId,
                $message,
                date("Y-m-d H:i:s"),
                $cost,
                $result->status,
                $result->messageId
            );
        } else {
            $failedRecipients[] = $result->number;
            error_log("SMS failed for {$result->number}: {$result->status}");
        }
    }

    // Log transaction
    if (!empty($logData)) {
        $logFile = __DIR__ . "/sms_api_log.txt";
        file_put_contents($logFile, implode("\n", $logData) . "\n", FILE_APPEND);
    }

    // Update user balance
    $pdo->prepare("UPDATE sms_users SET bal = bal - ? WHERE airtimeid = ?")
        ->execute([$totalCost, $userId]);

    Database::disconnect();

    // Return response
    if (!empty($failedRecipients) && count($failedRecipients) === $numberOfSMS) {
        jsonResponse(false, "All SMS deliveries failed", ["cost" => 0]);
    } else {
        $responseMsg = "SMS sent successfully to {$successfulRecipients} recipient(s)";
        if (!empty($failedRecipients)) {
            $responseMsg .= " (failed: " . implode(", ", $failedRecipients) . ")";
        }
        jsonResponse(true, $responseMsg, [
            "cost"            => $totalCost,
            "recipients"      => $successfulRecipients,
            "balance_after"   => number_format($balance - $totalCost, 2)
        ]);
    }

} catch (Throwable $e) {
    Database::disconnect();
    jsonResponse(false, "SMS gateway error: " . $e->getMessage());
} catch (Exception $e) {
    Database::disconnect();
    error_log("SMS API Error: " . $e->getMessage());
    jsonResponse(false, "Unexpected server error occurred");
}

// End of API Endpoint
?>';

file_put_contents($f, $c);
echo "Done writing api_sendsms.php";