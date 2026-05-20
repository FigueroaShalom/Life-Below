<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // CONFIG SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // 🔴 CAMBIA ESTO
    $mail->Username = 'lifebelow5of@gmail.com';
    $mail->Password = 'noiw voss xahn lqjx';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // DESTINATARIO
    $mail->setFrom('lifebelow50f@gmail.com', 'HYDRON');
    $mail->addAddress('dsalgado4@ucol.mx');

    // CONTENIDO
    $mail->isHTML(true);
    $mail->Subject = 'Prueba HYDRON';
    $mail->Body = '🔥 Correo funcionando correctamente con PHPMailer';

    $mail->send();
    echo "✔ Correo enviado correctamente";
} catch (Exception $e) {
    echo "❌ Error: {$mail->ErrorInfo}";
}