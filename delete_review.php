<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

$id = $_GET['id'];

$res = mysqli_query($conn,"SELECT * FROM reviews WHERE id='$id'");
$review = mysqli_fetch_assoc($res);

if($_SESSION['user_id'] != $review['buyer_id']){
    die("Unauthorized");
}

mysqli_query($conn,"DELETE FROM reviews WHERE id='$id'");
header("Location: property_details.php?id=".$review['property_id']);
