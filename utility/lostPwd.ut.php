<?php 
require_once('F:/Programming/xampp/htdocs/.git/mediaio/PHPMailer/src/PHPMailer.php');
    require '../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\SMTP;

    function generateRandomString($length = 25) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    

    if (isset($_POST['pwdLost-submit'])){
        $TOKEN = generateRandomString();
        require 'dbHandler.ut.php';

        $username = $_POST['userName'];
        $emailAddr = $_POST['emailAddr'];
        if(empty($username) || empty($emailAddr)){
            header("Location: ../profile/lostPwd.php?error=emptyField");
            exit();
        }else{
            //Check if this PWD is the correct one

            $sql = "UPDATE users SET TOKEN='$TOKEN' WHERE usernameUsers='$username' AND emailUsers='$emailAddr'";

            if (!mysqli_query($conn,$sql))
            {
            echo("Error description: " . mysqli_error($conn));
            }else{
            $to = $emailAddr;

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
            /* Set the mail sender. */
            $mail->setFrom('arpadmedia@gmail.com', 'mediaIO cron');
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->addAddress($emailAddr, 'Felhasználó');
            $mail->IsHTML(true); 
// Subject
$mail->Subject = 'MediaIO - Elfelejtett jelszó';

// Message
$mail->Body = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$username.'!</h3><p>
 Ezúton értesítünk, hogy fiókodhoz új jelszót igényeltek.
 Az új jelszavadat az alábbi tokennel tudod beállítani, a 2. lépésben:</p>
 <h1>'.$TOKEN.'</h1>
  <h6>Ez egy automatikus üzenet. Kérjük ne válaszolj rá.<br>Üdvözlettel: <br> Arpad Media Admin</h6>


  ne kódolj hajnalban. ha ezt látod, ez annak az eredménye.
</body>
</html>
';

if (!$mail->send())
{
   /* PHPMailer error. */
   echo $mail->ErrorInfo;
}

// To send HTML mail, the Content-type header must be set
$Mail_headers[] = 'MIME-Version: 1.0';
$Mail_headers[] = 'From: arpadmedia.io@gmail.com';
$Mail_headers[] = 'Content-type: text/html; charset=utf-8';

//$headers[] = 'Bcc: gutasi.guti@gmail.com';

                    }
        mysqli_close($conn);
        header("Location: ../profile/lostPwd.php?error=none");
        exit();
    }
}
?>