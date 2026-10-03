<?php
require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/auth.php';
require 'database.php';
require_once dirname(__DIR__) . '/hawlastke.php';
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
<?php
if(isset($_SESSION['access_level']))
{ ?>
<h2>HAWLAST SAAS</H2>
<p><a href="/saas/" class="btn btn-success">Home</a></p>

<?php
//IF ADMINISTRATOR 
if ($_SESSION['access_level'] == 2 ) {
$pdo = Database::connect();

// SQL query to calculate the average success sales from December 20, 2024
$sql = "
    SELECT 
        AVG(daily_sales) AS average_daily_success_sales
    FROM (
        SELECT 
            DATE(`date`) AS sale_date, 
            SUM(amount) AS daily_sales
        FROM airtime_transactions
        WHERE `date` >= '2024-12-20'
          AND status = 'Success'
        GROUP BY DATE(`date`)
    ) AS daily_totals;
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch the result
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Extract the average daily success sales
$averageDailySuccessSales = $result['average_daily_success_sales'];

// Output the result
echo "The average daily sales from 20th December 2024 is: " . number_format($averageDailySuccessSales, 2);

// Updated SQL query to include total success sales
$sql = "SELECT 
        DATE(`date`) AS report_date, 
        COUNT(DISTINCT `phone`) AS daily_unique_phones,
        (SELECT COUNT(DISTINCT `phone`) 
         FROM airtime_transactions AS sub
         WHERE sub.`date` <= main.`date` AND sub.`date` >= '2024-12-20') AS cumulative_unique_phones,
        SUM(CASE WHEN `status` = 'Success' THEN `amount` ELSE 0 END) AS daily_success_amount
    FROM airtime_transactions AS main
    WHERE `date` >= '2024-12-20'
    GROUP BY DATE(`date`)
    ORDER BY report_date ASC;";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch the data as an associative array
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="chart-container" style="width: 80%; margin: 0 auto; position: relative;">
    <canvas id="phoneChart"></canvas>
</div>
<script>
    // Pass PHP data to JavaScript
    const data = <?php echo json_encode($data); ?>;

    // Extract labels (dates), daily unique phones, cumulative unique phones, and daily success amounts
    const labels = data.map(item => item.report_date);
    const dailyUniquePhones = data.map(item => item.daily_unique_phones);
    const cumulativeUniquePhones = data.map(item => item.cumulative_unique_phones);
    const dailySuccessAmounts = data.map(item => item.daily_success_amount);

    // Chart.js configuration
    const ctx = document.getElementById('phoneChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Daily Unique Phones',
                    data: dailyUniquePhones,
                    borderColor: 'rgba(22, 192, 192, 1)',
                    backgroundColor: 'rgba(22, 192, 192, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Cumulative Unique Phones',
                    data: cumulativeUniquePhones,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Daily Success Amounts',
                    data: dailySuccessAmounts,
                    borderColor: 'rgba(254, 162, 235, 1)',
                    backgroundColor: 'rgba(254, 162, 235, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true, // Allow the chart to scale freely
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Count / Amount'
                    },
                    beginAtZero: true
                }
            }
        }
    });
</script>

<br/>
<br/>
<br/>
<?php 
}
}

?>
<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="https://www.hawlast.com/saas/">www.hawlast.com/saas</a></div>
</div>
</div>
</div>
</body></html>