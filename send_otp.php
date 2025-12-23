<?php
session_start();
include "db.php";

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    // Check email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        echo errorBox("Email not found!");
        exit;
    }

    // Generate OTP
    //$otp = rand(100000, 999999);
    //$otp_expire = date("Y-m-d H:i:s", time() + 300); // 5 minutes

    // Save OTP
    //$up = $conn->prepare("UPDATE users SET otp=?, otp_expire=? WHERE email=?");
    //$up->bind_param("iss", $otp, $otp_expire, $email);
    //$up->execute();
    
    $otp = (string) rand(100000, 999999);
$otp_expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$up = $conn->prepare("UPDATE users SET otp=?, otp_expire=? WHERE email=?");
$up->bind_param("sss", $otp, $otp_expire, $email);
$up->execute();

if ($up->affected_rows <= 0) {
    die("OTP not saved in DB");
}




   // if ($up->affected_rows == 0) {
       // echo errorBox("Failed to save OTP in database!");
       // exit;
   // }

    // Send OTP using Gmail SMTP
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;

        // ONLY USE THIS EMAIL — FIXED ONE
        $mail->Username = "zihadmuzahid2003@gmail.com";
        $mail->Password = "lrwahodmfbngcnxp";

        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom("zihadmuzahid2003@gmail.com", "Real Estate System");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Your OTP Code";
        $mail->Body = "
        <div style='padding: 15px; font-family: Arial;'>
            <h2>Your OTP Code</h2>
            <p>Your OTP is:</p>
            <h1 style='background:#eee; padding:10px; display:inline-block;'>$otp</h1>
            <p>This OTP will expire in <b>5 minutes</b>.</p>
        </div>
        ";

        $mail->send();

        echo successBox("OTP sent to your email! Redirecting...");
        echo "<script>
            setTimeout(function(){
                window.location='verify_otp.php?email=$email';
            }, 2000);
        </script>";

    } catch (Exception $e) {
        echo errorBox("Email sending failed: " . $mail->ErrorInfo);
    }
}

// UI COMPONENTS
function errorBox($msg) {
    return "
    <div style='width:380px; margin:60px auto; 
         background:#ffe6e6; padding:20px; border-left:5px solid #ff4c4c; 
         border-radius:8px; font-family:Arial;'>
         <h3 style='margin-top:0;color:#d40000;'>Error</h3>
         <p>$msg</p>
         <a href='forgot_password.php' style='color:#1877f2;'>Go Back</a>
    </div>";
}

function successBox($msg) {
    return "
    <div style='width:380px; margin:60px auto; 
         background:#e6ffe6; padding:20px; border-left:5px solid #1fa82b; 
         border-radius:8px; font-family:Arial;'>
         <h3 style='margin-top:0;color:#0b7d20;'>Success</h3>
         <p>$msg</p>
    </div>";
}
?>
