<?php
session_start();


session_unset();
session_destroy();


/*** redirect ***/
header("Location: login.php");
?>