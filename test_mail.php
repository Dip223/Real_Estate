<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;

    $mail->Username = "zihadmuzahid2003@gmail.com";       // your Gmail
    $mail->Password = "lrwahodmfbngcnxp";                 // your App Password

    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->SMTPDebug = 2;       // SHOW FULL ERROR
    $mail->Debugoutput = 'html';

    // Email Receiver
    $mail->setFrom("zihadmuzahid2003@gmail.com");
    $mail->addAddress("mehedi183012.2003@gmail.com");  // replace with your own email to test

    $mail->isHTML(true);
    $mail->Subject = "SMTP Test Email";
    $mail->Body = "If you see this, SMTP is working!";

    $mail->send();
    echo "SUCCESS — Email Sent";

} catch (Exception $e) {
    echo "ERROR: " . $mail->ErrorInfo;
}
?>
