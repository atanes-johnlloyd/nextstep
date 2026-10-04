<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendResetEmail($toEmail, $resetLink) {
    $mail = new PHPMailer(true);
    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // ⚠️ Use an app password, not your Gmail password
        $mail->Username = 'johnlloydatanes159@gmail.com';
        $mail->Password = 'bxjd smng lpoy lmdy'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email setup
        $mail->setFrom('yourgmail@gmail.com', 'NextStep Support');
        $mail->addAddress($toEmail);
        $mail->Subject = 'NextStep Password Reset';
        $mail->Body = "Click the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
