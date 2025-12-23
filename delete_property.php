<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "real_estate_db", 3307);

if (!$conn) {
    die("Database Error: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo "You must login first!";
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid property ID!";
    exit();
}

$property_id = $_GET['id'];
$seller_id = $_SESSION['user_id'];

// Delete property only if it belongs to this seller
$sql = "DELETE FROM property WHERE property_id='$property_id' AND seller_id='$seller_id'";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Property deleted successfully!'); window.location='seller_dashboard.php';</script>";
} else {
    echo "<script>alert('Delete failed!'); window.location='seller_dashboard.php';</script>";
}
?>
