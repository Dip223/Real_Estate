<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* ✅ DELETE REVIEW */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM reviews WHERE review_id='$id'");
    header("Location: admin_reviews.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Reviews</title>
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

        table{
            width:100%;
            border-collapse:collapse;
            background:white;
            margin-top:25px;
            box-shadow:0 0 8px rgba(0,0,0,0.1);
        }

        th,td{
            padding:12px;
            text-align:left;
            border-bottom:1px solid #ddd;
        }

        th{
            background:#2563eb;
            color:white;
        }

        .btn{
            padding:6px 12px;
            border-radius:5px;
            text-decoration:none;
            color:white;
            font-size:14px;
        }

        .btn.red{ background:#dc2626; }
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
        <h2>Manage Reviews</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Buyer</th>
            <th>Property</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php
        $result = mysqli_query($conn, 
            "SELECT reviews.*, users.name AS buyer_name, property.title AS property_title 
             FROM reviews 
             JOIN users ON reviews.buyer_id = users.user_id 
             JOIN property ON reviews.property_id = property.property_id 
             ORDER BY reviews.review_id DESC"
        );

        while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>
            <td><?php echo $row['review_id']; ?></td>
            <td><?php echo $row['buyer_name']; ?></td>
            <td><?php echo $row['property_title']; ?></td>
            <td><?php echo $row['rating']; ?> ⭐</td>
            <td><?php echo $row['review_text']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="admin_reviews.php?delete=<?php echo $row['review_id']; ?>" 
                   class="btn red" 
                   onclick="return confirm('Delete this review?')">
                   Delete
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
