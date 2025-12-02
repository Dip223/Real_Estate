<?php
session_start();
$conn = mysqli_connect("localhost","root","","real_estate_db",3307);

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result)==1){
        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] == 'seller'){
                header("Location: seller_dashboard.php");
            }elseif($user['role'] == 'buyer'){
                header("Location: buyer_dashboard.php");
            }else{
                header("Location: admin_dashboard.php");
            }

        }else{
            $error = "Invalid Email or Password!";
        }
    }else{
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Real Estate</title>

    <style>
        body{
            margin:0;
            height:100vh;
            background: linear-gradient(120deg,#00c6ff,#0072ff);
            display:flex;
            justify-content:center;
            align-items:center;
            font-family: Arial, sans-serif;
        }

        .login-box{
            background:white;
            width:350px;
            padding:35px;
            border-radius:10px;
            box-shadow:0px 5px 15px rgba(0,0,0,0.4);
            text-align:center;
        }

        input{
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
            background:#0072ff;
            color:white;
            border:none;
            border-radius:5px;
            font-size:16px;
            cursor:pointer;
        }

        button:hover{
            background:#005ad6;
        }

        h2{
            margin-bottom:20px;
        }

        .error{
            color:red;
            margin-bottom:10px;
        }

        .footer-text{
            margin-top:15px;
            font-size:13px;
            color:#666;
        }

        .register-link{
            margin-top:10px;
            font-size:14px;
        }

        .register-link a{
            color:#0072ff;
            text-decoration:none;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Real Estate Login</h2>

    <?php if(isset($error)){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <div class="register-link">
        New user? <a href="register.php">Create Account</a>
    </div>

    <div class="footer-text">
        © 2025 Real Estate System
    </div>
</div>

</body>
</html>

