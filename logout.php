<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <meta http-equiv="refresh" content="2;url=login.php">
    <style>
        body{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg,#ff512f,#dd2476);
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .logout-box{
            background:white;
            padding:30px 40px;
            border-radius:10px;
            text-align:center;
            box-shadow:0px 5px 15px rgba(0,0,0,0.3);
        }

        h2{
            color:#333;
        }

        p{
            margin-top:10px;
            color:gray;
        }
    </style>
</head>
<body>

<div class="logout-box">
    <h2>✅ Successfully Logged Out</h2>
    <p>Redirecting to login page...</p>
</div>

</body>
</html>
