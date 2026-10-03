<?php
/**
 * AJAX endpoint for the public "Buy airtime" page (buyairtime.php).
 *
 * The page is a three step M-PESA flow:
 *   1. recipient number -> 2. confirm + amount -> 3. paybill 822490 prompt
 * The buyer pays on their own phone, Safaricom calls our C2B callback
 * (pesa/confirmation.php -> saas/airtime/buyapinew.php), which sends the airtime
 * and records it in airtime_transactions. This endpoint only validates input and
 * reports that recorded row back while the page polls for it.
 *
 * It never sends airtime itself and it never charges anybody.
 */
require_once __DIR__ . '/hawlastke.php';
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['ok' => false, 'error' => 'POST only.']));
}

// Session token issued with the page, so a third party cannot drive this endpoint.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_POST['token']) || empty($_SESSION['airtime_token'])
    || !hash_equals((string) $_SESSION['airtime_token'], (string) $_POST['token'])) {
    http_response_code(403);
    exit(json_encode(['ok' => false, 'error' => 'Your session expired. Reload the page.']));
}

$action = isset($_POST['action']) ? (string) $_POST['action'] : '';

switch ($action) {

    /* ---- step 1: the recipient number ------------------------------- */
    case 'check_number':

        $phone = trim((string) ($_POST['phone'] ?? ''));
        $local = validAirtimeNumber($phone);

        if ($local === false) {
            exit(json_encode([
                'ok'    => false,
                'error' => 'Enter a 10 digit number, for example 0720401869.',
            ]));
        }

        exit(json_encode([
            'ok'    => true,
            'local' => $local,
        ]));
/* ---- step 2: the amount ------------------------------------------ */
    case 'check_amount':

        $phone = trim((string) ($_POST['phone'] ?? ''));
        $local = validAirtimeNumber($phone);

        if ($local === false) {
            exit(json_encode([
                'ok'    => false,
                'error' => 'That number is not a valid 10 digit number.',
            ]));
        }

        $amount = trim((string) ($_POST['amount'] ?? ''));

        // Digits only, no decimals, no thousand separators.
        if (!preg_match('/^\d{1,6}$/', $amount)) {
            exit(json_encode([
                'ok'    => false,
                'error' => 'Enter whole numbers only. No decimals and no commas.',
            ]));
        }

        $amount = (int) $amount;

        if ($amount < 5 || $amount > 2000) {
            exit(json_encode([
                'ok'    => false,
                'error' => 'The amount must be between KES 5 and KES 2,000.',
            ]));
        }

        // Remember the request so step 3 and the status poller agree on the number.
        $_SESSION['airtime_pending'] = [
            'phone'  => $local,
            'amount' => $amount,
            'since'  => time(),
        ];

        exit(json_encode([
            'ok'      => true,
            'phone'   => $local,
            'amount'  => $amount,
            'paybill' => AIRTIME_PAYBILL,
        ]));


    /* ---- step 3: poll for the delivered airtime ---------------------- */
    case 'status':

        $pending = $_SESSION['airtime_pending'] ?? null;

        if (!is_array($pending) || empty($pending['phone'])) {
            exit(json_encode([
                'ok'    => false,
                'error' => 'Start again from the top.',
                'reset' => true,
            ]));
        }

        // +254 form is what confirmation.php hands to the airtime gateway.
        $phone = add254($pending['phone']);

        $stmt = $pdo->prepare(
            "SELECT id, date, amount, discount, status, requestid
               FROM airtime_transactions
              WHERE phone = ?
                AND airtimeid = 10
                AND date >= FROM_UNIXTIME(?)
           ORDER BY id DESC
              LIMIT 1"
        );
        $stmt->execute([$phone, (int) $pending['since']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            exit(json_encode([
                'ok'        => true,
                'delivered' => false,
            ]));
        }

        // The paybill top-up covers the M-PESA charge, so the delivered amount is
        // normally a little above what was paid. Show both.
        $topUp = calculateAirtime($pending['amount']);

        exit(json_encode([
            'ok'        => true,
            'delivered' => true,
            'failed'    => strcasecmp((string) $row['status'], 'Sent') !== 0,
            'record'    => [
                'id'        => (int) $row['id'],
                'date'      => date('j M Y, g:ia', strtotime((string) $row['date'])),
                'paid'      => (int) $pending['amount'],
                'delivered' => (int) $row['amount'],
                'discount'  => (float) $row['discount'],
                'status'    => (string) $row['status'],
                'requestid' => (string) $row['requestid'],
                'topup'     => (int) $topUp,
            ],
        ]));


    default:
        http_response_code(400);
        exit(json_encode(['ok' => false, 'error' => 'Unknown action.']));
}

/**
 * Accept a Kenyan number as 10 digits (0720401869 / 254720401869 / +254720401869)
 * and return it in local 10 digit form.
 *
 * @return string|false
 */
function validAirtimeNumber(string $input) {
    $digits = preg_replace('/[^0-9]/', '', $input);

    if ($digits === null || !preg_match('/^\d+$/', $digits)) {
        return false;
    }

    // 2547XXXXXXXX -> 07XXXXXXXX
    if (strpos($digits, '254') === 0) {
        $digits = '0' . substr($digits, 3);
    }

    // Must be exactly 10 digits starting with 0 and a 7 or 1 (mobile range).
    if (!preg_match('/^0[17]\d{8}$/', $digits)) {
        return false;
    }

    return $digits;
}