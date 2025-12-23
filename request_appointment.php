<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: login.php");
    exit;
}

$buyer_id = $_SESSION['user_id'];
$property_id = $_GET['property_id'];

// get seller id
$q = mysqli_query($conn, "SELECT seller_id FROM property WHERE property_id=$property_id");
$p = mysqli_fetch_assoc($q);
$seller_id = $p['seller_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $date = $_POST['visit_date'];
    $time = $_POST['visit_time'];

    $stmt = $conn->prepare("
        INSERT INTO appointments (property_id, buyer_id, seller_id, visit_date, visit_time)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiss", $property_id, $buyer_id, $seller_id, $date, $time);
    $stmt->execute();

    echo "<script>alert('Appointment Requested!'); 
          window.location='buyer_dashboard.php';</script>";
}
?>

<link rel="stylesheet" href="glass.css">

<div class="glass-box">
    <h2>Request Appointment</h2>

    <form method="POST">
        <input type="date" name="visit_date" required>
        <input type="time" name="visit_time" required>
        <button type="submit">Send Request</button>
    </form>
</div>
