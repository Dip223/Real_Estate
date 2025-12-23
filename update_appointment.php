<?php
session_start();
include "db.php";

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller'){
    exit("Unauthorized access");
}

$appointment_id = $_GET['id'];
$status = $_GET['s']; // approved | rejected

/* =========================
   UPDATE STATUS
========================= */
mysqli_query($conn, "
    UPDATE appointments 
    SET status='$status' 
    WHERE appointment_id='$appointment_id'
");

/* =========================
   FETCH DETAILS
========================= */
$q = mysqli_query($conn, "
    SELECT 
        a.visit_date,
        a.visit_time,
        u.email,
        u.name AS buyer_name,
        p.title AS property_title
    FROM appointments a
    JOIN users u ON a.buyer_id = u.user_id
    JOIN property p ON a.property_id = p.property_id
    WHERE a.appointment_id='$appointment_id'
");

$row = mysqli_fetch_assoc($q);

/* =========================
   SEND EMAIL (APPROVE / REJECT)
========================= */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;

    // 🔐 CHANGE THESE
    $mail->Username = "zihadmuzahid2003@gmail.com";
    $mail->Password = "lrwahodmfbngcnxp"; // app password

    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("zihadmuzahid2003@gmail.com", "Real Estate System");
    $mail->addAddress($row['email'], $row['buyer_name']);

    $mail->isHTML(true);

    /* -------- APPROVED -------- */
    if($status == 'approved'){
        $mail->Subject = "Appointment Approved ✅";
        $mail->Body = "
        <div style='font-family:Arial; padding:20px'>
            <h2>Hello {$row['buyer_name']},</h2>
            <p>Your appointment has been <b style='color:green;'>APPROVED</b>.</p>

            <p><b>Property:</b> {$row['property_title']}</p>
            <p><b>Date:</b> {$row['visit_date']}</p>
            <p><b>Time:</b> {$row['visit_time']}</p>

            <br>
            <p>Please be on time.</p>
            <p style='color:#777'>Real Estate Management System</p>
        </div>
        ";
    }

    /* -------- REJECTED -------- */
    if($status == 'rejected'){
        $mail->Subject = "Appointment Rejected ❌";
        $mail->Body = "
        <div style='font-family:Arial; padding:20px'>
            <h2>Hello {$row['buyer_name']},</h2>
            <p>Your appointment request has been <b style='color:red;'>REJECTED</b>.</p>

            <p><b>Property:</b> {$row['property_title']}</p>

            <p>You may try requesting another time or different property.</p>

            <br>
            <p style='color:#777'>Real Estate Management System</p>
        </div>
        ";
    }

    $mail->send();

} catch (Exception $e) {
    // silently ignore mail error
}

/* =========================
   REDIRECT
========================= */
header("Location: seller_dashboard.php");
exit;
