<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['access_level']);
unset($_SESSION['valid_user']);
session_destroy();
/*** redirect ***/
header("Location: https://www.hawlast.com/saas/");
?>