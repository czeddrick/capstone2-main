<?php
session_start();
session_unset();
session_destroy();
header("Location: ../main/user_login.php");
exit;
?>
