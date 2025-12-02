<?php
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(isset($_POST['register'])){

    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role  = $_POST['role'];

    // 🔐 Secure password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email='$email'";
    $run   = mysqli_query($conn,$check);

    if(mysqli_num_rows($run) > 0){
        $error = "Email already exists!";
    }else{

        $sql = "INSERT INTO users (name,email,phone,password,role,status)
                VALUES ('$name','$email','$phone','$password','$role','active')";

        if(mysqli_query($conn,$sql)){
            header("Location: login.php");
        }else{
            $error = "Registration Failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | Real Estate</title>

    <style>
        body{
            margin:0;
            height:100vh;
            background: linear-gradient(120deg,#11998e,#38ef7d);
            display:flex;
            justify-content:center;
            align-items:center;
            font-family: Arial, sans-serif;
        }

        .register-box{
            background:white;
            width:380px;
            padding:35px;
            border-radius:10px;
            box-shadow:0px 5px 15px rgba(0,0,0,0.4);
            text-align:center;
        }

        input, select{
            width:100%;
            padding:10px;
            margin:10px 0;
            border:1px solid #ccc;
            border-radius:5px;
            font-size:16px;
        }

        button{
            width:100%;
            padding:10px;
            background:#11998e;
            color:white;
            border:none;
            border-radius:5px;
            font-size:16px;
            cursor:pointer;
        }

        button:hover{
            background:#0d7f6f;
        }

        h2{
            margin-bottom:20px;
        }

        .error{
            color:red;
            margin-bottom:10px;
        }

        .login-link{
            margin-top:15px;
            font-size:14px;
        }

        .login-link a{
            color:#11998e;
            text-decoration:none;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Create Account</h2>

    <?php if(isset($error)){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
            <option value="admin">Admin</option>
        </select>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="register">Register</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>
