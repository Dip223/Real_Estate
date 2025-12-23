<?php
session_start();
include "db.php";

if (!isset($_GET['email'])) {
    echo "<script>alert('Invalid Request'); window.location='login.php';</script>";
    exit;
}

$email = $_GET['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $otp = trim($_POST['otp']);

    // Fetch OTP
    $stmt = $conn->prepare("SELECT otp, otp_expire FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $data = $stmt->get_result();

    if ($data->num_rows === 0) {
        $error = "User not found!";
    } else {

        $row = $data->fetch_assoc();
        $db_otp = $row['otp'];
        $expiry = $row['otp_expire'];

        if ($db_otp === NULL) {
            $error = "OTP not generated. Register again!";
        }
        elseif ($otp !== $db_otp) {
            $error = "Incorrect OTP. Try again!";
        }
        elseif (strtotime($expiry) < time()) {
            $error = "OTP has expired! Please register again.";
        }
        else {
            // Verification success
            $update = $conn->prepare("
                UPDATE users 
                SET is_verified=1, otp=NULL, otp_expire=NULL 
                WHERE email=?
            ");
            $update->bind_param("s", $email);
            $update->execute();

            echo "<script>alert('Your account has been verified successfully!'); window.location='login.php';</script>";
            exit();
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
<title>Verify Account | Real Estate</title>

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

.verify-box {
    position: relative;
    z-index: 2;
    width: 380px;
    padding: 35px;
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
    font-size: 24px;
    margin-bottom: 20px;
}

input {
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
    background: linear-gradient(135deg,#66ccff,#3366ff);
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
    background: linear-gradient(135deg,#3366ff,#66ccff);
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

<div class="verify-box">

    <h2>Email Verification</h2>

    <?php if(isset($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify Account</button>
    </form>

</div>

</body>
</html>

