<?php
require_once('../PHPMailer/src/PHPMailer.php');
require '../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//insert.php
session_start();
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }

if(isset($_POST["date"]) && isset($_POST["user"]) && isset($_POST["task"]))
{
    //Először nézzük meg, létezik-e a felhasználó:
    $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
    $user=$_POST["user"];
    $result = $conn->query("SELECT emailUsers, firstName FROM users WHERE userNameUsers='$user'");
    $conn->close();
 if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $to=$row['emailUsers'];
        $nev=$row['firstName'];
    }
     
    $connect = new PDO("mysql:host=localhost;dbname=mediaio", $setup['dbUserName'], $setup['dbPassword']);
 
 $query = "
 INSERT INTO feladatok 
 (Datum, Szemely, Feladat) 
 VALUES (:date, :user, :task)
 ";
 $statement = $connect->prepare($query);
 $statement->execute(
  array(
   ':date'  => $_POST['date'],
   ':user' => $_POST['user'],
   ':task' => $_POST['task']
  )
 );
 //E-mail küldése a felhasznßálónak
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
$mail->Username   = $setup['app_email'];
$mail->Password   = $setup['app_email_pass'];
 $mail->Body = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$nev.'!</h3>
  <p>Új feladatot kaptál:
  <table max-width="600px" display: block; margin: 0 auto ; border="1px solid black" cellspacing="0" cellpadding="0">
  <th style="text-align: center;">Dátum 📅</th>
  <th style="text-align: center;">Feladat 📝</th>
  <tr><td style="text-align: center;">'.$_POST['date'].'</h6>'.'</td><td style="text-align: center;">'.$_POST['task'].'</td></tr>
</table>

Ha szerinted ez az e-mail nem releváns, vagy hibás, jelezd azt a vezetőségnek.
  <h5>Üdvözlettel: <br> Arpad Media Admin👋</h5>
</body>
</html>
';


$mail->isHTML(true);
$mail->setFrom($setup['app_email'], 'mediaIO');
$mail->FromName = "mediaIO";
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
$mail->addAddress($to, $nev);
$mail->Subject = 'mediaIO - Új feladat!';
//mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, implode("\r\n", $headers));
try {
  $mail->send();
  echo "3";
} catch (Exception $e) {
  echo "Mailer Error: " . $mail->ErrorInfo;
}
}else{
    echo "1";// Nincs ilyen felhasználó
}
$connect=null;
}else{
    echo "2";//Üres cella, vagy formátumhiba.
}

?>