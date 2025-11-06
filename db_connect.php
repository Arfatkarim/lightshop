<?php
// db_connect.php
$servername = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "shop_db";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
