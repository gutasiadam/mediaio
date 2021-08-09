<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();

require("../../PHPMailer/src/PHPMailer.php");
require("../../PHPMailer/src/SMTP.php");
require("../../PHPMailer/src/Exception.php");

//include '../../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\SMTP;
$data = json_decode(stripslashes($_POST['data']),true);
//echo $data;

$mail = new PHPMailer();
$mail->SMTPOptions = array(
    'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
    )
    );


$mail->Mailer = "smtp";
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "arpadmedia.io@gmail.com";
$mail->Password   = "xlr8VGA%";
$mail->IsHTML(true);
/* Set the mail sender. */
$mail->setFrom('arpadmedia@gmail.com', 'mediaIO damage Report');
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
/* Add a recipient. */
$mail->addAddress('gutasi.guti@gmail.com', 'Media Admin');

/* Set the subject. */
$mail->Subject = 'bejelentett sérülés: '.$data['UID'];

/* Set the mail message body. */
$mail->Body = $_SESSION['fullName'].' sérülést jelentett be a <strong>'.$data['Nev'].' ('.$data['UID'].')</strong> tárgyon! <br> Leírás: '.$data['err_description'];

/* Finally send the mail. */
if (!$mail->send())
{
   /* PHPMailer error. */
   echo $mail->ErrorInfo;
}else{
    echo 200;
}


?>