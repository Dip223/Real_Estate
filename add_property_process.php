<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$title = $_POST['title'];
$location = $_POST['location'];
$price = $_POST['price'];
$category = $_POST['category'];
$seller_id = $_SESSION['user_id'];

$sql = "INSERT INTO properties (title, location, price, category, seller_id)
        VALUES ('$title', '$location', '$price', '$category', '$seller_id')";

if ($conn->query($sql)) {
    echo "✅ Property Added Successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}
?>
