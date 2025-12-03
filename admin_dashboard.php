<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
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
            transition:0.3s;
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

        .topbar h2{
            margin:0;
        }

        .logout{
            background:#ef4444;
            color:white;
            padding:10px 18px;
            border-radius:6px;
            text-decoration:none;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
            gap:20px;
            margin-top:25px;
        }

        .card{
            background:white;
            padding:25px;
            border-radius:10px;
            box-shadow:0 0 8px rgba(0,0,0,0.1);
            text-align:center;
        }

        .card h3{
            margin:0;
            font-size:16px;
            color:#6b7280;
        }

        .card p{
            font-size:32px;
            margin:10px 0 0;
            font-weight:bold;
            color:#111827;
        }

        .actions{
            margin-top:30px;
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(240px,1fr));
            gap:20px;
        }

        .box{
            background:white;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 8px rgba(0,0,0,0.1);
        }

        .box h3{
            margin-top:0;
        }

        .btn{
            display:inline-block;
            margin-top:10px;
            background:#2563eb;
            color:white;
            padding:10px 15px;
            border-radius:6px;
            text-decoration:none;
        }

        .btn.red{
            background:#dc2626;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>ADMIN PANEL</h2>
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="admin_users.php">👥 Manage Users</a>
    <a href="admin_properties.php">🏘 Manage Properties</a>
    <a href="admin_reviews.php">⭐ Manage Reviews</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <div class="topbar">
        <h2>Welcome, Admin</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Total Users</h3>
            <p>👤 120</p>
        </div>

        <div class="card">
            <h3>Total Properties</h3>
            <p>🏘 58</p>
        </div>

        <div class="card">
            <h3>Total Reviews</h3>
            <p>⭐ 214</p>
        </div>

        <div class="card">
            <h3>Pending Approvals</h3>
            <p>⏳ 6</p>
        </div>
    </div>

    <div class="actions">
        <div class="box">
            <h3>Manage Users</h3>
            <p>View, block, or remove users.</p>
            <a href="admin_users.php" class="btn">Open</a>
        </div>

        <div class="box">
            <h3>Manage Properties</h3>
            <p>Approve, edit, or delete properties.</p>
            <a href="admin_properties.php" class="btn">Open</a>
        </div>

        <div class="box">
            <h3>Manage Reviews</h3>
            <p>Remove spam or fake reviews.</p>
            <a href="admin_reviews.php" class="btn">Open</a>
        </div>

        <div class="box">
            <h3>System Control</h3>
            <p>View logs & configuration.</p>
            <a href="#" class="btn red">Settings</a>
        </div>
    </div>

</div>

</body>
</html>
