<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(!$conn){
    die("Database Error: " . mysqli_connect_error());
}

if(!isset($_SESSION['user_id'])){
    echo "You must login first!";
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch seller properties
$sql = "SELECT * FROM property WHERE seller_id='$seller_id' ORDER BY property_id DESC";
$result = mysqli_query($conn, $sql);

$appointment_sql = mysqli_query($conn, "
    SELECT a.*, 
           u.name AS buyer_name, 
           u.phone AS buyer_phone,
           p.title AS property_title
    FROM appointments a
    JOIN users u ON a.buyer_id = u.user_id
    JOIN property p ON a.property_id = p.property_id
    WHERE a.seller_id = '$seller_id'
    ORDER BY a.created_at DESC
");



?>

<!DOCTYPE html>
<html>
<head>
<title>Seller Dashboard</title>

<style>
    body{
        margin:0;
        font-family:Arial, sans-serif;
        background:#f7f9fc;
    }

    .header{
        background:#4A6CF7;
        color:white;
        padding:20px 40px;
        font-size:28px;
        font-weight:bold;
    }

    .container{
        padding:30px;
    }

    .top-btn{
        background:#4A6CF7;
        padding:12px 20px;
        color:white;
        text-decoration:none;
        border-radius:8px;
        font-weight:bold;
        transition:0.3s;
    }
    .top-btn:hover{
        background:#3b55c4;
    }

    .grid{
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));
        gap:25px;
        margin-top:25px;
    }

    .card{
        background:white;
        border-radius:15px;
        box-shadow:0 4px 10px rgba(0,0,0,0.1);
        overflow:hidden;
        transition:0.3s;
    }
    .card:hover{
        transform:scale(1.02);
    }

    .card img{
        width:100%;
        height:200px;
        object-fit:cover;
    }

    .card-body{
        padding:20px;
    }

    .title{
        font-size:22px;
        font-weight:bold;
        margin-bottom:10px;
        color:#333;
    }

    .price{
        color:#4A6CF7;
        font-size:20px;
        font-weight:bold;
        margin-bottom:8px;
    }

    .info{
        color:#555;
        font-size:14px;
        margin-bottom:5px;
    }

    .btn-box{
        display:flex;
        justify-content:space-between;
        margin-top:15px;
    }

    .btn{
        padding:8px 12px;
        text-decoration:none;
        border-radius:6px;
        font-size:14px;
        font-weight:bold;
        transition:0.3s;
    }

    .edit{
        background:#28a745;
        color:white;
    }
    .edit:hover{
        background:#1e7e34;
    }

    .delete{
        background:#dc3545;
        color:white;
    }
    .delete:hover{
        background:#b52a37;
    }
</style>

</head>
<body>

<div class="header">Seller Dashboard</div>

<div class="container">

    <a class="top-btn" href="add_property.php">+ Add New Property</a>




    <!-- ==========================
     APPOINTMENT REQUESTS
========================== -->
<h2 style="margin-top:30px;">📅 Appointment Requests</h2>

<table>
<tr>
    <th>Buyer</th>
    <th>Phone</th>
    <th>Property</th>
    <th>Date</th>
    <th>Time</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php if(mysqli_num_rows($appointment_sql) > 0){ ?>
<?php while($a = mysqli_fetch_assoc($appointment_sql)){ ?>
<tr>
   <td><?php echo $a['buyer_name']; ?></td>
    <td><?php echo $a['buyer_phone']; ?></td>
    <td><?php echo $a['property_title']; ?></td>
    <td><?php echo $a['visit_date']; ?></td>
    <td><?php echo $a['visit_time']; ?></td>
    <td><b><?php echo ucfirst($a['status']); ?></b></td>
    <td>
        <?php if($a['status'] == 'pending'){ ?>
            <a class="btn edit" 
               href="update_appointment.php?id=<?php echo $a['appointment_id']; ?>&s=approved">
               Approve
            </a>
            <a class="btn delete" 
               href="update_appointment.php?id=<?php echo $a['appointment_id']; ?>&s=rejected">
               Reject
            </a>
        <?php } else { echo "-"; } ?>
    </td>
</tr>
<?php } } else { ?>
<tr>
    <td colspan="7" align="center">No appointment requests</td>
</tr>
<?php } ?>
</table>   


   
 




    <div class="grid">
    <?php 
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $image = $row['image'] ? "uploads/".$row['image'] : "no-image.jpg";
    ?>
    
        <div class="card">
            <img src="<?php echo $image; ?>" alt="Property Image">
            
            <div class="card-body">
                <div class="title"><?php echo $row['title']; ?></div>
                <div class="price">৳ <?php echo number_format($row['price']); ?></div>
                <div class="info">📍 <?php echo $row['location']; ?></div>
                <div class="info">🏠 <?php echo $row['property_type']; ?> | 🛏 <?php echo $row['bedrooms']; ?> Beds</div>
                <div class="info">📏 <?php echo $row['square_feet']; ?> sq ft</div>

                <div class="btn-box">
                    <a class="btn edit" href="edit_property.php?id=<?php echo $row['property_id']; ?>">Edit</a>
                    <a class="btn delete" onclick="return confirm('Delete this property?')" 
                       href="delete_property.php?id=<?php echo $row['property_id']; ?>">Delete</a>
                </div>
            </div>
        </div>

    <?php 
        }
    } else {
        echo "<h3>No properties added yet.</h3>";
    }
    ?>
    </div>

</div>

</body>
</html>
