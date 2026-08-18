<?php
 /*** start the session ***/
    session_start();
    if(isset($_SESSION['adaccess_level']))
        {
                $log_link = 'logout.php';
                $log_link_name = 'Log Out';
                $reg_link = '#';
                $reg_name = $_SESSION['valid_user'];
        }
        else
        {
                $log_link = 'login.php';
                $log_link_name = 'Log In';
                $reg_link = 'register.php';
                $reg_name = 'Register';
	      $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
              $_SESSION['redirect'] = $url;
	      header("Location: login.php");
   	      exit;
        }
ob_start();
require_once('classes/paginator.class.php');
require '../config.php';
require '../hawlastke.php';

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Search results</title>
<meta name=robots content=noindex,nofollow />
<link rel="shortcut icon" href="images/favicon.ico" type=image/x-icon />
<link rel="stylesheet" type="text/css" href="images/view.css" media="all">
</head>
<body id="main_body" >
<img id="top" src="images/top.png" alt="">
<a href="./">Home</a> 
<a href="listed.php">Listed</a> 
<a href="user.php?pwd=1">Account</a> 
<a href="addnew.php">Add new</a> 
<a href="<?php echo $log_link; ?>"><?php echo $log_link_name; ?></a>  
  
<table> <tbody><tr> 
<td align="left"> 
<form action="search.php" method="GET" name="search">
<input name="search" value=""> 
<input type="submit" value="Search" style="font-size: 15px; height: 1.50em; vertical-align: middle;"> 
</form>
</td> </tr> </tbody>
</table> 
<div id="form_list">
<?php
if (isset($_REQUEST['search'])) {
    $search = $_REQUEST['search'];

$countQuery = "SELECT COUNT(*) AS total_rows FROM renewals WHERE 
        firstname LIKE '%$search%' OR 
        lastname LIKE '%$search%' OR 
        url LIKE '%$search%' OR 
        email LIKE '%$search%' OR 
        emailalt LIKE '%$search%' OR 
        phone LIKE '%$search%'";
        
$stmt = $pdo->prepare($countQuery);
$stmt->execute();
$count_row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRows = $count_row['total_rows'];


    $pages = new Paginator();
    $pages->items_total = $totalRows;
    $pages->mid_range = 9;
    $pages->paginate();
    echo $pages->display_pages();

    // Fetch the paginated results
    $resultQuery = "SELECT * FROM renewals WHERE 
        firstname LIKE '%$search%' OR 
        lastname LIKE '%$search%' OR 
        url LIKE '%$search%' OR 
        email LIKE '%$search%' OR 
        emailalt LIKE '%$search%' OR 
        phone LIKE '%$search%'
    ";
    $stmt = $pdo->prepare($resultQuery);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo '<table border="1" width="96%">';
        echo '<tr bgcolor="#fff">
                <td colspan="10"><p align="center"><small><font face="Verdana"><b>Oldest to latest account</b></font></small></td>
              </tr>';
        echo '<tr bgcolor="#fff">
                <td width="10%"><p align="center"><small><font face="Verdana">First Name</font></small></td>
                <td width="10%"><p align="center"><small><font face="Verdana">Last Name</font></small></td>
                <td width="10%"><p align="center"><small><font face="Verdana">Service</font></small></td>
                <td width="10%"><p align="center"><small><font face="Verdana">Cost</font></small></td>
                <td width="10%"><p align="center"><small><font face="Verdana">Email</font></small></td>
                <td width="10%"><p align="center"><small><font face="Verdana">Phone</font></small></td>
                <td width="10%"><p align="center"><small><font face="Verdana">Renewal Date</font></small></td>
                <td width="5%"><p align="center"><small><font face="Verdana">Edit</font></small></td>
                <td width="5%"><p align="center"><small><font face="Verdana">Renew</font></small></td>
                <td width="5%"><p align="center"><small><font face="Verdana">Status</font></small></td>
              </tr>';
        
        foreach ($results as $row) {
            $color = "#D8DBFE"; // You can alternate colors if needed
            echo "<tr bgcolor=\"$color\">
                    <td><small><font face=\"Verdana\">{$row['firstname']}</font></small></td>
                    <td><small><font face=\"Verdana\">{$row['lastname']}</font></small></td>
                    <td><small><font face=\"Verdana\">{$row['service']}</font></small></td>
                    <td><small><font face=\"Verdana\">{$row['cost']}</font></small></td>
                    <td><small><font face=\"Verdana\">{$row['url']}<br>{$row['email']}</font></small></td>
                    <td><small><font face=\"Verdana\">{$row['phone']}</font></small></td>
                    <td><small><font face=\"Verdana\">{$row['renewal_date']}</font></small></td>
                    <td><form method=\"get\" action=\"edit.php\">
                            <input type=\"hidden\" name=\"id\" value=\"{$row['id']}\">
                            <input type=\"submit\" value=\"Edit\">
                        </form></td>
                    <td><form method=\"get\" action=\"listed.php\">
                            <input type=\"hidden\" name=\"id\" value=\"{$row['id']}\">
                            <input type=\"submit\" value=\"Renew\">
                        </form></td>
                    <td>Status Here</td>
                  </tr>";
        }
        echo '</table>';
        echo $pages->display_pages();
    } else {
        echo "<h2>No records found for '$search'</h2>";
    }
}
?>

</div>
<div id="footer">
By <a href="http://www.hawlast.com">Hawlast.com</a>
</div>
</div>
<img id="bottom" src="images/bottom.png" alt="">
</body>
</html>