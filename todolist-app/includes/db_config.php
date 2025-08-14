<?php
// db_config.php - Database connection configuration
$servername = "172.31.22.43";
$username = "Samuel200595786";
$password = "vb9dRKhq-o";
$dbname = "Samuel200595786";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>