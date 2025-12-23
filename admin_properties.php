<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

/* ✅ APPROVE PROPERTY */
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    mysqli_query($conn, "UPDATE property SET status='available' WHERE property_id='$id'");
    header("Location: admin_properties.php");
}

/* ✅ REJECT PROPERTY */
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    mysqli_query($conn, "UPDATE property SET status='rejected' WHERE property_id='$id'");
    header("Location: admin_properties.php");
}

/* ✅ DELETE PROPERTY */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM property WHERE property_id='$id'");
    header("Location: admin_properties.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Properties</title>
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

        .btn.green{ background:#16a34a; }
        .btn.red{ background:#dc2626; }
        .btn.gray{ background:#6b7280; }

        .status{
            padding:4px 10px;
            border-radius:12px;
            font-size:12px;
            color:white;
        }

        .available{ background:#16a34a; }
        .pending{ background:#f59e0b; }
        .rejected{ background:#dc2626; }
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
        <h2>Manage Properties</h2>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Price</th>
            <th>Location</th>
            <th>Seller</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        $result = mysqli_query($conn, "SELECT property.*, users.name AS seller_name 
                                      FROM property 
                                      JOIN users ON property.seller_id = users.user_id 
                                      ORDER BY property.property_id DESC");

        while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>
            <td><?php echo $row['property_id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td>৳ <?php echo number_format($row['price']); ?></td>
            <td><?php echo $row['location']; ?></td>
            <td><?php echo $row['seller_name']; ?></td>
            <td>
                <span class="status <?php echo $row['status']; ?>">
                    <?php echo ucfirst($row['status']); ?>
                </span>
            </td>
            <td>
                <?php if($row['status'] == 'pending'){ ?>
                    <a href="admin_properties.php?approve=<?php echo $row['property_id']; ?>" class="btn green">Approve</a>
                    <a href="admin_properties.php?reject=<?php echo $row['property_id']; ?>" class="btn gray">Reject</a>
                <?php } ?>

                <a href="admin_properties.php?delete=<?php echo $row['property_id']; ?>" 
                   class="btn red" 
                   onclick="return confirm('Delete this property?')">
                   Delete
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
