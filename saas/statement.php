<?php
require 'config.php';
require 'database.php';
require_once dirname(__DIR__) . '/hawlastke.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Statement Run</title>
 <meta charset="utf-8">
<meta name="description" content="">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
        <link rel="shortcut icon" href="img/favicon.ico" type=image/x-icon />
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
<?php
if(isset($_SESSION['access_level']))
{ ?>
<h2>HAWLAST SAAS</H2>
<p><a href="/saas/" class="btn btn-success">Home</a></p>

<?php
if ($_SESSION['access_level'] == 2 ) {

$airtimeid = $_SESSION['user_id']; 

    $pdo = Database::connect();

    // Pagination variables
    $limit = 100; // Number of results per page
    $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
    $start = ($page - 1) * $limit;

    $sql = "SELECT * FROM airtime_transactions ORDER BY date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $total_records = $stmt->rowCount();

    // Paginated query
    $sql = "SELECT * FROM airtime_transactions ORDER BY date DESC LIMIT :limit OFFSET :start";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    Database::disconnect();

    // Calculate total pages
    $total_pages = ceil($total_records / $limit);

    ?>

    <table>
 <tr>
<td>Date</td>
<td>Receivers no</td>
<td>Status</td>
<td>Amount</td>
<td>MPESA</td>
</tr>

        <?php foreach ($results as $row) { ?>
            <tr>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td><?php echo $row['TransID']; ?></td>
            </tr>
        <?php } ?>

    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php } ?>
    </div>

    <?php

}
 
} else { 
?>
<table>
<tr>
<td>Date</td>
<td>Receivers no</td>
<td>Status</td>
<td>Amount</td>
</tr>
<?php
$airtimeid = $_SESSION['user_id']; 
$pdo = Database::connect();
$sql = "SELECT * FROM airtime_transactions WHERE airtimeid ='$airtimeid' ORDER BY date DESC LIMIT 50";
foreach ($pdo->query($sql) as $row) {
echo '<tr><td>'. $row['date'] . '</td>';
echo '<td>'. $row['phone'] . '</td>';
echo '<td>'. $row['status'] . '</td>';
echo '<td>'. $row['amount'] . '</td>
</tr>';
Database::disconnect();
} echo "</table>";

}
?>

<div id="footer">
<div class="copyright">&copy; <?php echo copyrightYear(2010); ?> <a href="http://www.hawlast.com/saas/">www.hawlast.com/saas</a></div>
</div>
</div>
</div>
</body></html>