


<?php

// SHOW ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    // 1️⃣ Check email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $error = "Email not found!";
    } else {

        // 2️⃣ Generate OTP
        $otp = strval(rand(100000, 999999)); // STRING
        $otpExpire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // 3️⃣ Save OTP in DB
        $update = $conn->prepare(
            "UPDATE users SET otp=?, otp_expire=? WHERE email=?"
        );

        if (!$update) {
            die("Prepare failed: " . $conn->error);
        }

        $update->bind_param("sss", $otp, $otpExpire, $email);
        $update->execute();

        if ($update->affected_rows > 0) {

            // 4️⃣ Send OTP via Gmail SMTP
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;

                // 🔑 YOUR WORKING GMAIL
                $mail->Username = "zihadmuzahid2003@gmail.com";
                $mail->Password = "lrwahodmfbngcnxp"; // App password

                $mail->SMTPSecure = "tls";
                $mail->Port = 587;

                $mail->setFrom("zihadmuzahid2003@gmail.com", "Real Estate System");
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Password Reset OTP";
                $mail->Body = "
                    <div style='font-family:Arial'>
                        <h2>Password Reset</h2>
                        <p>Your OTP is:</p>
                        <h1 style='letter-spacing:3px;'>$otp</h1>
                        <p>This OTP will expire in <b>5 minutes</b>.</p>
                    </div>
                ";

                $mail->send();

                header("Location: verify_otp.php?email=$email");
                exit;

            } catch (Exception $e) {
                $error = "Email sending failed: " . $mail->ErrorInfo;
            }

        } else {
            $error = "OTP could not be saved!";
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
<title>Forgot Password | Real Estate</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    margin: 0;
    height: 100vh;
    background: url("assets/login_bg.jpg") no-repeat center center/cover;
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

.box {
    position: relative;
    z-index: 2;
    width: 380px;
    padding: 40px;
    border-radius: 18px;
    background: rgba(255,255,255,0.15);
    box-shadow: 0 0 35px rgba(0,0,0,0.4);
    backdrop-filter: blur(12px);
    color: #fff;
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
}

input {
    width: 100%;
    padding: 14px;
    margin: 10px 0;
    border-radius: 10px;
    border: none;
    outline: none;
    background: rgba(255,255,255,0.25);
    color: white;
    font-size: 15px;
}

input::placeholder { color: #ddd; }

button {
    width: 100%;
    padding: 14px;
    margin-top: 10px;
    background: linear-gradient(135deg, #00d2ff, #3a7bd5);
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: linear-gradient(135deg, #3a7bd5, #00d2ff);
    transform: scale(1.04);
}

.error {
    margin-top: 10px;
    padding: 10px;
    background: rgba(255,0,0,0.35);
    border-left: 4px solid #ff3333;
    border-radius: 5px;
    text-align: center;
}
</style>

</head>
<body>

<div class="overlay"></div>

<div class="box">

    <h2>Forgot Password</h2>

    <?php if(!empty($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send OTP</button>
    </form>

</div>

</body>
</html>




















