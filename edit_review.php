<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

$id = $_GET['id'];

$sql = "SELECT * FROM reviews WHERE id='$id'";
$res = mysqli_query($conn,$sql);
$review = mysqli_fetch_assoc($res);

if($_SESSION['user_id'] != $review['buyer_id']){
    die("Unauthorized Access!");
}

if(isset($_POST['update'])){
    $rating = $_POST['rating'];
    $review_text = mysqli_real_escape_string($conn,$_POST['review_text']);

    mysqli_query($conn,"UPDATE reviews SET rating='$rating', review_text='$review_text' WHERE id='$id'");
    header("Location: property_details.php?id=".$review['property_id']);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Review</title>
<style>
body{
    background:linear-gradient(120deg,#36d1dc,#5b86e5);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:Arial;
}
.box{
    background:white;
    padding:30px;
    width:400px;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.3);
}
textarea,select,button{
    width:100%;
    margin:10px 0;
    padding:10px;
}
button{
    background:#5b86e5;
    color:white;
    border:none;
}
</style>
</head>

<body>
<div class="box">
<h2>Edit Your Review</h2>

<form method="POST">
<select name="rating">
<option value="1" <?php if($review['rating']==1) echo "selected"; ?>>1 Star</option>
<option value="2" <?php if($review['rating']==2) echo "selected"; ?>>2 Stars</option>
<option value="3" <?php if($review['rating']==3) echo "selected"; ?>>3 Stars</option>
<option value="4" <?php if($review['rating']==4) echo "selected"; ?>>4 Stars</option>
<option value="5" <?php if($review['rating']==5) echo "selected"; ?>>5 Stars</option>
</select>

<textarea name="review_text"><?php echo $review['review_text']; ?></textarea>

<button name="update">Update Review</button>
</form>
</div>
</body>
</html>
