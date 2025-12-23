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

// Fetch property
$sql = "SELECT * FROM property WHERE property_id='$property_id' AND seller_id='$seller_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Property not found!";
    exit();
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Property</title>

<style>
body{
    margin:0;
    background:#f4f6f9;
    font-family:Arial;
}

.container{
    width:600px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

h2{
    margin-top:0;
    color:#4A6CF7;
}

label{
    font-weight:bold;
    margin-top:15px;
    display:block;
}

input, textarea, select{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    margin-top:20px;
    padding:12px 20px;
    width:100%;
    background:#4A6CF7;
    border:none;
    border-radius:8px;
    color:white;
    font-weight:bold;
    font-size:16px;
    cursor:pointer;
}
button:hover{
    background:#3b55c4;
}

.current-img{
    margin-top:10px;
}
.current-img img{
    width:100%;
    max-height:220px;
    object-fit:cover;
    border-radius:8px;
}
</style>
</head>

<body>

<div class="container">

<h2>Edit Property</h2>

<form action="" method="POST" enctype="multipart/form-data">

    <label>Title:</label>
    <input type="text" name="title" value="<?php echo $row['title']; ?>" required>

    <label>Description:</label>
    <textarea name="description" required><?php echo $row['description']; ?></textarea>

    <label>Price:</label>
    <input type="number" name="price" value="<?php echo $row['price']; ?>" required>

    <label>Location:</label>
    <input type="text" name="location" value="<?php echo $row['location']; ?>" required>

    <label>Property Type:</label>
    <select name="property_type" required>
        <option <?php if($row['property_type']=="Flat") echo "selected"; ?>>Flat</option>
        <option <?php if($row['property_type']=="Land") echo "selected"; ?>>Land</option>
        <option <?php if($row['property_type']=="Apartment") echo "selected"; ?>>Apartment</option>
    </select>

    <label>Bedrooms:</label>
    <input type="number" name="bedrooms" value="<?php echo $row['bedrooms']; ?>" required>

    <label>Square Feet:</label>
    <input type="number" name="square_feet" value="<?php echo $row['square_feet']; ?>" required>

    <label>Current Image:</label>
    <div class="current-img">
        <img src="uploads/<?php echo $row['image']; ?>">
    </div>

    <label>Upload New Image (optional):</label>
    <input type="file" name="image">

    <button type="submit" name="update">Update Property</button>

</form>

<?php

if(isset($_POST['update'])){

    $title = $_POST['title'];
    $desc  = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $ptype = $_POST['property_type'];
    $bed   = $_POST['bedrooms'];
    $size  = $_POST['square_feet'];

    // If seller uploads new image
    if(!empty($_FILES['image']['name'])){
        $newName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $newName);
    } else {
        // Keep old image
        $newName = $row['image'];
    }

    // Update DB
    $updateSQL = "UPDATE property SET
                    title='$title',
                    description='$desc',
                    price='$price',
                    location='$location',
                    property_type='$ptype',
                    bedrooms='$bed',
                    square_feet='$size',
                    image='$newName'
                  WHERE property_id='$property_id' AND seller_id='$seller_id'";

    if(mysqli_query($conn, $updateSQL)){
        echo "<script>alert('Updated successfully!'); window.location='seller_dashboard.php';</script>";
    } else {
        echo "<p style='color:red;'>Error updating property.</p>";
    }
}
?>

</div>

</body>
</html>
