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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Property</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body{
            margin:0;
            font-family:'Poppins', sans-serif;
            background:#f4f6fb;
        }

        .container{
            width:90%;
            max-width:900px;
            margin:40px auto;
        }

        .card{
            background:white;
            padding:30px;
            border-radius:16px;
            box-shadow:0 8px 18px rgba(0,0,0,0.08);
        }

        h2{
            margin:0 0 20px 0;
            font-size:26px;
            font-weight:600;
            color:#222;
        }

        label{
            font-weight:500;
            margin-bottom:6px;
            display:block;
            color:#333;
        }

        input, select, textarea{
            width:100%;
            padding:12px;
            border-radius:10px;
            border:1px solid #ccc;
            margin-bottom:18px;
            font-size:15px;
            background:#fafafa;
        }

        textarea{
            height:120px;
        }

        button{
            background:#4a6cf7;
            color:white;
            padding:14px 20px;
            border:none;
            border-radius:12px;
            font-size:16px;
            cursor:pointer;
            width:100%;
            font-weight:600;
            transition:0.2s;
        }

        button:hover{
            background:#3453e8;
        }

        .success{
            background:#d4f8db;
            color:#1b7e2f;
            padding:12px;
            border-radius:10px;
            margin-top:10px;
        }

        .error{
            background:#ffd8d8;
            color:#b90000;
            padding:12px;
            border-radius:10px;
            margin-top:10px;
        }

        .back{
            text-decoration:none;
            display:inline-block;
            margin-bottom:20px;
            color:#4a6cf7;
            font-weight:500;
        }

    </style>

</head>
<body>

<div class="container">

    <a class="back" href="seller_dashboard.php">← Back to Dashboard</a>

    <div class="card">

        <h2>Add New Property</h2>

        <form action="" method="POST" enctype="multipart/form-data">

            <label>Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" required></textarea>

            <label>Price (BDT)</label>
            <input type="number" name="price" required>

            <label>Location (City / Area)</label>
            <input type="text" name="location" required>

            <label>Property Type</label>
            <select name="property_type" required>
                <option value="Flat">Flat</option>
                <option value="Land">Land</option>
                <option value="Apartment">Apartment</option>
            </select>

            <label>Bedrooms</label>
            <input type="number" name="bedrooms" required>

            <label>Bathrooms</label>
            <input type="number" name="bathrooms" required>

            <label>Floor</label>
            <input type="number" name="floor">

            <label>Square Feet</label>
            <input type="number" name="square_feet" required>

            <label>Upload Image</label>
            <input type="file" name="image" required>

            <button type="submit" name="submit">Add Property</button>

        </form>

        <?php
        if(isset($_POST['submit'])){

            $title = $_POST['title'];
            $desc  = $_POST['description'];
            $price = $_POST['price'];
            $location = $_POST['location'];
            $ptype = $_POST['property_type'];
            $bed   = $_POST['bedrooms'];
            $bath = $_POST['bathrooms'];
            $floor = $_POST['floor'];
            $size  = $_POST['square_feet'];

            // IMAGE UPLOAD
            $imgName = $_FILES['image']['name'];
            $tmpName = $_FILES['image']['tmp_name'];

            $newImgName = time() . "_" . $imgName;

            move_uploaded_file($tmpName, "uploads/" . $newImgName);

            // INSERT QUERY
            $sql = "INSERT INTO property 
                    (seller_id, title, description, price, location, property_type, bedrooms,bathrooms, floor, square_feet, image)
                    VALUES 
                    ('$seller_id', '$title', '$desc', '$price', '$location', '$ptype', '$bed', '$bath', '$floor', '$size', '$newImgName')";

            if(mysqli_query($conn, $sql)){
                echo "<div class='success'>Property added successfully!</div>";
            } else {
                echo "<div class='error'>Error: " . mysqli_error($conn) . "</div>";
            }
        }
        ?>

    </div>

</div>

</body>
</html>
