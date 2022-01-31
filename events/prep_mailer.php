<?php
require_once('../PHPMailer/src/PHPMailer.php');
include ('../network/ip.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



$mail = new PHPMailer(true);
$to = $_SESSION['email'];
$mail->From = "arpadmedia.io@gmail.com";
$mail->FromName = "mediaIO";
$mail->addAddress($to);
$mail->isHTML(true);
$mail->Subject = 'Esemény hozzáadása - '.$_POST['title'].'';
$mail->CharSet = 'UTF-8';
// Message
$mail->Body= '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$_SESSION['UserUserName'].'!</h3><p>
 Kattints az alábbi linkre, hogy megerősítsd a(z)'.$_POST['title'].' esemény létrehozását</p>
 <table style="border: 1px solid black; width: 50%">
 <tr>
 <th>Esemény neve</th>
 <th>Esemény kezdete</th>
 <th>Esemény vége<td></th>
 </tr>
 <tr>
 <td>'.$_POST['title'].'</h6>'.'</td><td>'.$_POST['start'].'</td><td>'.$_POST['end'].'</td></tr>
 </table>
Kérlek ellenőrizd az az adatokat, mielőtt jóváhagyod az eseményt. Ezek a linkek csak a belső Wifin működnek!!
Ha az esemény adatait hibásan adtad meg, <a href="192.168.24.100/mediaio/events/prepFinalise.php?secureId='.$secureId.'&mode=del">kattints ide ❌</a>
<h2><a href="192.168.24.100/mediaio/events/prepFinalise.php?secureId='.$secureId.'&mode=add">Esemény hozzáadása ✔</a></h2>
  <h5>Üdvözlettel: <br> Arpad Media Admin</h5>
'.$ip_address.'
</body>
</html>
';

try {
  $mail->send();
  echo 200;
} catch (Exception $e) {
  echo "Mailer Error: " . $mail->ErrorInfo;
}



?>