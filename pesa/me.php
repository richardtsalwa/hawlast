<?php
require '../config.php';
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW"> 
<title>Store Statement</title> 
<style>
    /* General Table Styling */
    .table-container {
        width: 100%;
        overflow-x: auto; /* Enable horizontal scrolling for small screens */
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th, td {
        text-align: center;
        padding: 10px;
        border: 1px solid #ccc;
    }

    th {
        background-color: #f4f4f4;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        table {
            font-size: 14px; /* Adjust font size for small screens */
        }

        th, td {
            padding: 8px;
        }
    }

    @media (max-width: 480px) {
        table {
            font-size: 12px;
        }

        th, td {
            padding: 5px;
        }
    }

    /* Pagination Styling */
    .pagination {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }

    .page-link {
        margin: 0 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #333;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .page-link:hover {
        background-color: #f0f0f0;
    }
</style>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('#search').on('keyup', function () {
            let value = $(this).val().toLowerCase();
            $('#transaction-table tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

</head>
<body>
<?php 

// SQL query to fetch transactions
$sql = "SELECT MSISDN, TransTime FROM mpesaapi";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Initialize the $transactions array
$transactions = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Convert transatime to YYYY-MM-DD HH:MM:SS format
     // $transatime = substr($row["TransTime"], 0, 4) . "-" . substr($row["TransTime"], 4, 2) . "-" . substr($row["TransTime"], 6, 2) . " " . substr($row["TransTime"], 8, 2) . ":" . substr($row["TransTime"], 10, 2) . ":" . substr($row["TransTime"], 12, 2); 

    // Add transaction to the array
    $transactions[] = array(
        'phone_number' => $row["MSISDN"],
        'date' =>  $row["TransTime"],
    );
}

echo "<pre>";
//print_r($transactions);
echo "</pre>"; 

// Define the churn period (e.g., 30 days)
$churn_period = 100; // Days

// Calculate churn rate
$churn_rate = calculate_churn_rate($transactions, $churn_period);

// Display churn rate
echo "Churn Rate: " . number_format($churn_rate * 100, 2) . "%\n";

/*
 * Calculates churn rate based on transaction data.
 *
 * @param array $transactions Array of transactions with 'phone_number' and 'date' keys.
 * @param int $churn_period Churn period in days.
 * @return float Churn rate (between 0 and 1).
 */
 
function calculate_churn_rate($transactions, $churn_period) {
    // Group transactions by phone number
    $grouped_transactions = [];
    foreach ($transactions as $transaction) {
        $phone_number = $transaction['phone_number'];
        if (!isset($grouped_transactions[$phone_number])) {
            $grouped_transactions[$phone_number] = [];
        }
        $grouped_transactions[$phone_number][] = $transaction['date'];
    }

    // Calculate churned customers
    $churned_customers = 0;
    foreach ($grouped_transactions as $phone_number => $dates) {
        // Sort transaction dates in ascending order
        sort($dates);

        // Check for churn within the churn period
        $last_transaction_date = $dates[count($dates) - 1]; 
        $churned = true;
        foreach ($dates as $date) {
            $date_diff = date_diff(date_create($last_transaction_date), date_create($date))->days;
            if ($date_diff <= $churn_period) {
                $churned = false;
                break;
            }
        }

        if ($churned) {
            $churned_customers++;
        }
    }

    // Calculate churn rate
    $total_customers = count($grouped_transactions);
    if ($total_customers > 0) {
        return $churned_customers / $total_customers;
    } else {
        return 0;
    }
}

// Assuming $pdo is your established PDO connection

$sql = "SELECT MSISDN, MAX(TransTime) AS last_sale_date 
        FROM mpesaapi
        WHERE TransTime > '2024-12-19' AND TransTime < DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY MSISDN";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $distinct_phone_numbers = 0;
// Process the results
foreach ($results as $row) {
   //echo "Phone Number: " . $row['MSISDN'] . "<br>";
//echo "Last Sale Date: " . $row['last_sale_date'] . "<br><br>";
         $distinct_phone_numbers++; 
}
echo "Total Distinct Phone Numbers: " . $distinct_phone_numbers . "<br>";


if(isset($_GET['t'])) {
$token=$_GET['t']; 
$token = trim($_GET['t']);
$expected_token = "5699";
echo $token;
if ($token !== $expected_token) { 
//If the token doesn't match, stop processing
exit("Token mismatch. Processing stopped."); 
} 

} else {
die();
}

// Define the number of results per page
$results_per_page = 50;

// Determine the current page and calculate the offset
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Prepare the SQL query with LIMIT and OFFSET for pagination
$sql = "SELECT Auto, TransID, TransTime, TransAmount, BillRefNumber, InvoiceNumber, 
        ThirdPartyTransID, MSISDN, FirstName, OrgAccountBalance,confirmation_status 
        FROM mpesaapi 
        ORDER BY Auto DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $results_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of records for pagination
$total_stmt = $pdo->query("SELECT COUNT(*) FROM mpesaapi");
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $results_per_page);
?>

<!-- Search Input -->
<div>
    <form method="get" action="">
    <input type="hidden" name="t" value="5699">
    
    <input type="text" id="search" placeholder="Search by TransID, FirstName, MSISDN, or BillRefNumber">
    </form>
</div>

<!-- Table HTML -->
<table border="1" width="99%">
    <tr><td colspan="8">TRANSACTIONS</td></tr>
    <tr bgcolor="#fff">
        <th>TransID</th>
        <th>TransTime</th>
        <th>TransAmount</th>
        <th>BillRefNumber</th>
        <th>MSISDN</th>
        <th>FirstName</th>
        <th>OrgAccountBalance</th>
                <th>Status</th>
    </tr>
    <tbody id="transaction-table">
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['TransID']); ?></td>
                <td><?php echo htmlspecialchars($row['TransTime']); ?></td>
                <td><?php echo htmlspecialchars($row['TransAmount']); ?></td>
                <td><?php echo htmlspecialchars($row['BillRefNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['MSISDN']); ?></td>
                <td><?php echo htmlspecialchars($row['FirstName']); ?></td>
                <td><?php echo htmlspecialchars($row['OrgAccountBalance']);?></td>
                                <td><?php echo htmlspecialchars($row['confirmation_status']);?></td>
                                
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Pagination Links -->
<div>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?t=5699&page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
</body>
</html>