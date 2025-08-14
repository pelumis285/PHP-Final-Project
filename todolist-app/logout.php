<?php
// logout.php
// This simple script destroys the session and redirects the user to the homepage.
require_once './includes/csrf.php';
safe_session_start();

session_destroy();
header("Location: index.php");
exit();
?>