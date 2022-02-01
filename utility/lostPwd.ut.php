<?php 
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
if($serverType['type']=='dev'){
  $setup = parse_ini_file(realpath('../../../../mediaio-config/config.ini')); // @ Dev
  require_once('F:/Programming/xampp/htdocs/.git/mediaio/PHPMailer/src/PHPMailer.php');
}else{
  $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
  require_once('C:/xampp/htdocs/mediaio/PHPMailer/src/PHPMailer.php');
}
    require '../PHPMailer/src/SMTP.php';
    require '../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\Exception;
use PHPMailer\SMTP;

    function generateRandomString($length = 6) {
        return substr(str_shuffle(str_repeat($x='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    if (isset($_POST['pwdLost-submit'])){
        $TOKEN = generateRandomString();
        //require 'dbHandler.ut.php';
        $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);

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
                </body>
            </html>';

            if (!$mail->send())
            {/* PHPMailer error. */echo $mail->ErrorInfo;}

            // To send HTML mail, the Content-type header must be set
            //$Mail_headers[] = 'MIME-Version: 1.0';
            //$Mail_headers[] = 'From: arpadmedia.io@gmail.com';
            //$Mail_headers[] = 'Content-type: text/html; charset=utf-8';

                    }
        mysqli_close($conn);
        header("Location: ../profile/lostPwd.php?error=none");
        exit();
    }
}elseif(isset($_POST['pwdLost-change-submit'])){
    require 'dbHandler.ut.php';
    $TOKEN=$_POST['token'];
    $username=$_POST['userName'];
    $newPwd_text=$_POST['chPwd-1'];
    $newPwd_text_2=$_POST['chPwd-2'];
    if($newPwd_text!=$newPwd_text_2 || empty($newPwd_text) || empty($newPwd_text_2) || strlen($newPwd_text)<8){
        header("Location: ../profile/lostPwd.php?error=pwdNoMatch");
        exit();
    }
    $sql = "SELECT * FROM users WHERE usernameUsers='$username' AND TOKEN='$TOKEN';";
    $result = mysqli_query($conn, $sql);
    $num_rows = mysqli_num_rows($result);
    if($num_rows==1){
        $row = mysqli_fetch_array($result);
        $emailAddr=$row['emailUsers'];
        $hashedpwd = password_hash($newPwd_text, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET pwdUsers='$hashedpwd', TOKEN=NULL WHERE usernameUsers='$username' ;";}
        if (!mysqli_query($conn,$sql))
        {
        echo("Error description: " . mysqli_error($conn));
        }else{
            $to=$emailAddr;
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
            $mail->setFrom('arpadmedia@gmail.com', 'mediaIO');
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->addAddress($emailAddr, 'Felhasználó');
            $mail->IsHTML(true); 
            // Subject
            $mail->Subject = 'MediaIO - Új jelszót állítottál ne';

// Message
            $mail->Body = '
            <html>
            <head>
                <title>Arpad Media IO</title>
            </head>
                <body>
                    <h3>Kedves '.$username.'!</h3><p>
                    Új jelszavadat sikeresen beállítottuk.
            <h6>Ez egy automatikus üzenet. Kérjük ne válaszolj rá.<br>Üdvözlettel: <br> Arpad Media Admin</h6>
                </body>
            </html>';

            if (!$mail->send())
            {/* PHPMailer error. */echo $mail->ErrorInfo;}
        }
        header("Location: ../profile/lostPwd.php?error=none2");
        exit();
    }else{
        header("Location: ../profile/lostPwd.php?error=wtf");
        exit();
    }
?>