<?php
require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/auth.php';
require 'database.php';
//require 'functions.php';
require '../hawlastke.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Dashboard </title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>HAWLAST SAAS</H2>
<p><a href="/saas/" class="btn btn-success">Home</a></p>

<?php
//IF ADMINISTRATOR 
if ($_SESSION['access_level'] == 2 ) {
    
$message = "Friendly reminder:Buy airtime at zero transaction fees. Don’t miss out!Paybill 822490 Account: Your number FAQs-0720401869";

$pdo = Database::connect();

// SQL query to get the latest date for the phone numbers that appear after the specified date,
// and are older than 30 days from the current date (to detect churned users).
$sql = "SELECT phone, MAX(date) AS latest_date
        FROM airtime_transactions
        WHERE phone IN (
            SELECT DISTINCT phone
            FROM airtime_transactions
            WHERE date > '2024-12-20 01:00:00'
        )
        GROUP BY phone
        HAVING MAX(date) < NOW() - INTERVAL 30 DAY ORDER BY latest_date";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute();

echo $stmt->rowCount();

//die();

// Fetch and display the results
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Phone: " . $row["phone"] . " - Latest Date: " . $row["latest_date"] . "<br>";
        
       $phone = $row["phone"];
        
    //SEND AN SMS
    
//TumaSMS($message, $phone);

//END OF SEND AN SMS 

    }
} else {
    echo "No churned users found.";
}


}
?>

<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="https://www.hawlast.com/saas/">www.hawlast.com/saas</a></div>
</div>
</div>
</div>
</body></html>