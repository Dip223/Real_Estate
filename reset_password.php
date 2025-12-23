<?php
session_start();
include "db.php";

if (!isset($_GET['email'])) {
    die("Invalid request");
}

$email = $_GET['email'];

// When user submits new password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $otp = trim($_POST['otp']);
    $new_pass = trim($_POST['new_password']);
    $c_pass = trim($_POST['confirm_password']);

    if ($new_pass !== $c_pass) {
        $error = "Passwords do not match!";
    } else {
        // Check OTP validity
        $stmt = $conn->prepare("SELECT otp, otp_expire FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            $error = "User not found.";
        } else {

            // Check OTP correct + not expired
            if ($otp != $user['otp']) {
                $error = "Invalid OTP!";
            } elseif (strtotime($user['otp_expire']) < time()) {
                $error = "OTP expired. Try again!";
            } else {

                // Update password
                $hashed = password_hash($new_pass, PASSWORD_BCRYPT);

                $up = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expire=NULL WHERE email=?");
                $up->bind_param("ss", $hashed, $email);
                $up->execute();

                echo "<script>alert('Password reset successful! Please login now.'); 
                window.location='login.php';</script>";
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>

    <style>
        body {
            margin: 0;
            background: #f0f2f5;
            font-family: Arial, sans-serif;
        }

        .box {
            width: 380px;
            background: #fff;
            padding: 25px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0 0 8px #ccc;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #1877f2;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .error {
            background: #ffdddd;
            padding: 10px;
            border-left: 4px solid #ff4c4c;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>

<div class="box">

    <h2>Reset Password</h2>

    <?php if (!empty($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form action="" method="POST">

        <label>Enter OTP</label>
        <input type="number" name="otp" placeholder="6-digit OTP" required>

        <label>New Password</label>
        <input type="password" name="new_password" placeholder="Enter new password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm password" required>

        <button class="btn" type="submit">Reset Password</button>

    </form>

</div>

</body>
</html>

