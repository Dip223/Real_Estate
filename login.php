
<?php
session_start();

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "real_estate_db", 3307);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    // ✅ PREPARED STATEMENT (SECURE)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    if(!$stmt){
        die("Query Error: ".$conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $row = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($pass, $row['password'])) {

            // ✅ SET SESSION PROPERLY
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role']    = $row['role'];
            $_SESSION['name']    = $row['name'];
            $_SESSION['email']   = $row['email'];

            // ✅ ROLE BASED REDIRECT
            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            }
            elseif ($row['role'] === 'seller') {
                header("Location: seller_dashboard.php");
            }
            else {
                header("Location: buyer_dashboard.php");
            }
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "Email not found!";
    }
}
?>





<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login - Real Estate</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    margin: 0;
    padding: 0;
    height: 100vh;
    background: url("assets/building.jpg") no-repeat center center/cover;
    font-family: "Poppins", sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
}

.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(10, 10, 10, 0.55);
    backdrop-filter: blur(3px);
}

.login-box {
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
    margin-bottom: 25px;
    font-weight: 600;
    letter-spacing: 1px;
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

input::placeholder {
    color: #ddd;
}

button {
    width: 100%;
    padding: 14px;
    margin-top: 15px;
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

.links {
    text-align: center;
    margin-top: 15px;
}

.links a {
    color: #90e0ff;
    text-decoration: none;
    font-size: 14px;
}

.links a:hover {
    text-decoration: underline;
}

.error {
    margin-top: 10px;
    padding: 10px;
    background: rgba(255,0,0,0.3);
    border-left: 4px solid #ff4444;
    color: white;
    font-size: 14px;
    border-radius: 5px;
    text-align: center;
}
</style>

</head>
<body>

<div class="overlay"></div>

<div class="login-box">
    <h2>Login</h2>

    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form action="" method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <div class="links">
        <a href="register.php">Create an Account</a><br>
        <a href="forgot_password.php">Forgot Password?</a>
    </div>
</div>

</body>
</html>
