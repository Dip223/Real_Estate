<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

if(!isset($_GET['property_id'])){
    die("Property ID not found!");
}

$property_id = $_GET['property_id'];

if(isset($_POST['submit_review'])){

    $rating = $_POST['rating'];
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);

    $sql = "INSERT INTO reviews (property_id, buyer_id, rating, review_text)
            VALUES ('$property_id', '$buyer_id', '$rating', '$review_text')";

    if(mysqli_query($conn,$sql)){
        header("Location: property_details.php?id=$property_id");
        exit();
    }else{
        $error = "Failed to submit review!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Review</title>

    <style>
        body{
            margin:0;
            min-height:100vh;
            background: linear-gradient(120deg,#ff9966,#ff5e62);
            display:flex;
            justify-content:center;
            align-items:center;
            font-family: Arial, sans-serif;
        }

        .review-box{
            background:white;
            width:400px;
            padding:30px;
            border-radius:12px;
            box-shadow:0px 10px 25px rgba(0,0,0,0.3);
            text-align:center;
        }

        h2{
            margin-bottom:20px;
            color:#333;
        }

        select, textarea{
            width:100%;
            padding:10px;
            margin:10px 0;
            border:1px solid #ccc;
            border-radius:6px;
            font-size:15px;
        }

        textarea{
            resize:none;
            height:100px;
        }

        button{
            width:100%;
            padding:12px;
            margin-top:10px;
            background:#ff5e62;
            color:white;
            border:none;
            border-radius:6px;
            font-size:16px;
            cursor:pointer;
        }

        button:hover{
            background:#e0484c;
        }

        .error{
            color:red;
            margin-bottom:10px;
        }

        .back-link{
            margin-top:15px;
            font-size:14px;
        }

        .back-link a{
            color:#ff5e62;
            text-decoration:none;
        }

        .stars{
            font-size:20px;
            color:#ffbb00;
        }
    </style>
</head>
<body>

<div class="review-box">
    <h2>⭐ Add Your Review</h2>

    <?php if(isset($error)){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">

        <label><b>Rating</b></label><br>
        <select name="rating" required>
            <option value="">Select Rating</option>
            <option value="1">⭐ 1 Star</option>
            <option value="2">⭐⭐ 2 Stars</option>
            <option value="3">⭐⭐⭐ 3 Stars</option>
            <option value="4">⭐⭐⭐⭐ 4 Stars</option>
            <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
        </select>

        <label><b>Your Review</b></label>
        <textarea name="review_text" placeholder="Write your experience about this property..." required></textarea>

        <button type="submit" name="submit_review">Submit Review</button>
    </form>

    <div class="back-link">
        <a href="property_details.php?id=<?php echo $property_id; ?>">← Back to Property</a>
    </div>
</div>

</body>
</html>
