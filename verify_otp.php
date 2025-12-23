<?php
// SHOW ERRORS (REMOVE AFTER DEBUG)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // DB connect

$error = "";
$email = "";

if (isset($_GET['email'])) {
    $email = $_GET['email'];
}

// When OTP submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Fetch OTP from DB
    $stmt = $conn->prepare("SELECT otp, otp_expire FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        $error = "Email not found!";
    } else {
        $row = $res->fetch_assoc();

        // Check OTP match
        if ($otp != $row['otp']) {
            $error = "Invalid OTP!";
        }
        // Check time expiry
        else if (time() > strtotime($row['otp_expire'])) {
            $error = "OTP Expired!";
        }
        else {
            // OTP valid → go to reset password page
            echo "<script>alert('OTP Verified Successfully!');
                  window.location='reset_password.php?email=$email';
                  </script>";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verify OTP | Real Estate</title>

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
    background: linear-gradient(135deg, #f953c6, #b91d73);
    border: none;
    border-radius: 10px;
    font-size: 17px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: linear-gradient(135deg, #b91d73, #f953c6);
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

    <h2>Verify OTP</h2>

    <?php if(!empty($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">

        <input type="hidden" name="email" value="<?php echo $email; ?>">

        <input type="text" name="otp" placeholder="Enter OTP" required maxlength="6">

        <button type="submit">Verify OTP</button>
    </form>

</div>

</body>
</html>
