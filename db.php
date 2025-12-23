<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "real_estate_db";
$port = 3307;   

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
