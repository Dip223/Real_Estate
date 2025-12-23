<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* ✅ LIVE COUNTS */
$total_users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users"))['total'];
$total_buyers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='buyer'"))['total'];
$total_sellers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='seller'"))['total'];
$total_admins = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='admin'"))['total'];

$total_properties = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM property"))['total'];
$total_reviews = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM reviews"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#f4f6f9;
}

.sidebar{
    width:250px;
    height:100vh;
    background:#111827;
    position:fixed;
    padding-top:20px;
}

.sidebar h2{
    color:white;
    text-align:center;
    margin-bottom:30px;
}

.sidebar a{
    display:block;
    color:#cbd5e1;
    padding:14px 20px;
    text-decoration:none;
}

.sidebar a:hover{
    background:#2563eb;
    color:white;
}

.main{
    margin-left:250px;
    padding:30px;
}

.topbar{
    background:white;
    padding:15px 25px;
    border-radius:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 0 8px rgba(0,0,0,0.1);
}

.logout{
    background:#ef4444;
    color:white;
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
}

.boxes{
    margin-top:30px;
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
    gap:20px;
}

.box{
    padding:25px;
    border-radius:14px;
    color:white;
    font-size:22px;
    font-weight:bold;
    text-align:center;
    box-shadow:0 10px 15px rgba(0,0,0,0.12);
}

.users{background:#2563eb;}
.buyers{background:#10b981;}
.sellers{background:#f59e0b;}
.admins{background:#8b5cf6;}
.properties{background:#ec4899;}
.reviews{background:#ef4444;}

.box span{
    display:block;
    font-size:36px;
    margin-top:10px;
}
</style>
</head>

<body>

<!-- ✅ SIDEBAR -->
<div class="sidebar">
    <h2>ADMIN PANEL</h2>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="admin_users.php">👥 Manage Users</a>
    <a href="admin_properties.php">🏘 Manage Properties</a>
    <a href="admin_reviews.php">⭐ Manage Reviews</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- ✅ MAIN -->
<div class="main">

    <div class="topbar">
        <h2>Live Admin Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- ✅ LIVE STAT BOXES -->
    <div class="boxes">

        <div class="box users">
            Total Users
            <span><?php echo $total_users; ?></span>
        </div>

        <div class="box buyers">
            Total Buyers
            <span><?php echo $total_buyers; ?></span>
        </div>

        <div class="box sellers">
            Total Sellers
            <span><?php echo $total_sellers; ?></span>
        </div>

        <div class="box admins">
            Total Admins
            <span><?php echo $total_admins; ?></span>
        </div>

        <div class="box properties">
            Total Properties
            <span><?php echo $total_properties; ?></span>
        </div>

        <div class="box reviews">
            Total Reviews
            <span><?php echo $total_reviews; ?></span>
        </div>

    </div>

</div>

</body>
</html>

