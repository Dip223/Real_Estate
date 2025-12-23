<?php
session_start();
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['register'])) {

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $role     = $_POST['role'];
    $password = $_POST['password'];

    // Hash password
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

    // OTP
    $otp = strval(rand(100000, 999999));  // keep as STRING
    $otp_expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    // Check email exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $error = "Email already exists!";
    } else {

        // INSERT user
        $sql = $conn->prepare("
            INSERT INTO users (name, email, phone, password, role, status, otp, otp_expire, is_verified)
            VALUES (?, ?, ?, ?, ?, 'active', ?, ?, 0)
        ");

        // FIXED: otp + otp_expire MUST be strings => ssssss s
        $sql->bind_param("sssssss",             
      
            $name, 
            $email, 
            $phone, 
            $hashed_pass, 
            $role, 
            $otp, 
            $otp_expire
        );

        if ($sql->execute()) {

            // Send OTP email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mehedi.dip@northsouth.edu';
                $mail->Password = 'bedzeqqtpzzfrqgo'; // App pass
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('mehedi.dip@northsouth.edu', 'Real Estate System');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Your Account Verification OTP";
                $mail->Body = "
                    <h2>Hello $name,</h2>
                    <p>Your OTP:</p>
                    <h1>$otp</h1>
                    <p>This OTP expires in 5 minutes.</p>
                ";

                $mail->send();

                header("Location: verify.php?email=$email");
                exit();

            } catch (Exception $e) {
                $error = "OTP email sending failed!";
            }

        } else {
            $error = "Registration failed!";
        }
    }
}
?>





















<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Register | Real Estate</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    margin: 0;
    height: 100vh;
    background: url("assets/register_bg.jpg") no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: "Poppins", sans-serif;
}

.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(10,10,10,0.55);
    backdrop-filter: blur(3px);
}

.register-box {
    position: relative;
    z-index: 2;
    width: 420px;
    padding: 40px;
    border-radius: 18px;
    background: rgba(255,255,255,0.14);
    box-shadow: 0 0 35px rgba(0,0,0,0.45);
    backdrop-filter: blur(12px);
    color: white;
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(40px); }
    to   { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    font-size: 26px;
    margin-bottom: 20px;
}

input, select {
    width: 100%;
    padding: 13px;
    margin: 10px 0;
    background: rgba(255,255,255,0.25);
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 15px;
}

input::placeholder { color: #ddd; }

button {
    width: 100%;
    padding: 14px;
    margin-top: 15px;
    background: linear-gradient(135deg,#ff9966,#ff5e62);
    border: none;
    border-radius: 10px;
    font-size: 17px;
    color: white;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

button:hover {
    transform: scale(1.05);
    background: linear-gradient(135deg,#ff5e62,#ff9966);
}

.error {
    margin-top: 10px;
    padding: 10px;
    background: rgba(255,0,0,0.35);
    border-left: 4px solid #ff3333;
    border-radius: 5px;
    text-align: center;
}

.login-link {
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
}

.login-link a {
    color: #ffccaa;
    text-decoration: none;
    font-weight: 600;
}

</style>
</head>
<body>

<div class="overlay"></div>

<div class="register-box">

    <h2>Create Account</h2>

    <?php if(isset($error)) { ?>
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
