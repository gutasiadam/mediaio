<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';
require './PHPMailer/src/OAuth.php';

//require_once('./PHPMailer/src/PHPMailer.php');
$mail = new PHPMailer();
$mail->SMTPOptions = array(
    'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
    )
    );


$mail->Mailer = "smtp";
//$mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
$mail->AddAddress("gutasi.guti@gmail.com");
$mail->SMTPDebug  = 1;  
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "arpadmedia.io@gmail.com";
$mail->Password   = "xlr8VGA%";
//$mail->SMTPSecure = 'tls';

$mail->setFrom('arpadmedia.io@gmail.com', 'MediaiIO Admin');
$mail->addReplyTo('gutasi.guti@gmail.com', 'Gutási Ádám');

$mail->isHTML(true);

$mail->Subject = "PHPMailer SMTP test";
//$mail->addEmbeddedImage('path/to/image_file.jpg', 'image_cid');
$mail->Body = 'Mail body in HTML';
$mail->AltBody = 'This is the plain text version of the email content';

if(!$mail->send()){
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}else{
    echo 'Message has been sent';}

?>