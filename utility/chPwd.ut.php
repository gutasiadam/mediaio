<?php
namespace Mediaio;
use Mediaio\Core;
require_once __DIR__.'/../Core.php';
    if (isset($_POST['pwdCh-submit'])){
        //require 'dbHandler.ut.php';
        $serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
        if($serverType['type']=='dev'){
            $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
        }else{
            $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
        }
        $conn = mysqli_connect($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
        $username = $_SESSION['userId'];
        $ususname  = $_SESSION['UserUserName'];
        $oldpwd = $_POST['pwd-Old'];
        $password = $_POST['pwd-New'];
        $passwordrepeat = $_POST['pwd-New-Check'];

        if(empty($oldpwd) || empty($password) || empty($passwordrepeat)){
            header("Location: ../profile/chPwd.php?error=emptyField");
            exit();
        }else if ($password !== $passwordrepeat){
        header("Location: ../profile/chPwd.php?error=PasswordCheck");
        exit();
        }else if(strlen($password) < 8 ){
            header("Location: ../profile/chPwd.php?error=PasswordCheck");
            exit();
        }else{
            //Check if this PWD is the correct one

            $sql = "SELECT * FROM users WHERE usernameUsers=?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../profile/chPwd.phpp?error=SQLError1");
                exit();
            }else{
                mysqli_stmt_bind_param($stmt, "s", $ususname);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
                if($row = mysqli_fetch_assoc($result)){
                    $pwdcheck = password_verify($oldpwd, $row['pwdUsers']);


                    if ($pwdcheck == false){
                        header("Location: ../profile/chPwd.php?error=OldPwdError");


                    }else if ($pwdcheck == true){
                        $hashedpwd = password_hash($password, PASSWORD_BCRYPT); 
                        $sql = "UPDATE users SET pwdUsers='$hashedpwd' WHERE usernameUsers='$ususname';";
                    
                            if (!mysqli_query($conn,$sql))
                            {
                            echo("Error description: " . mysqli_error($conn));
                            }else{
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
                                <h3>Kedves '.$_SESSION['UserUserName'].'!</h3>
                                <p>Ezúton tájékoztatunk, hogy jelszavadat sikeresen megváltoztattad!</p>

                                Ha nem te változtattad meg a jelszavadat, azonnal jelezd azt a vezetőségnek!
                                <h5>Üdvözlettel: <br> Arpad Media Admin👋</h5>
                                </body>
                                </html>
                                ';
                                
                                
                                $mail->isHTML(true);
                                $mail->setFrom($setup['app_email'], 'mediaIO');
                                $mail->FromName = "mediaIO";
                                $mail->CharSet = 'UTF-8';
                                $mail->Encoding = 'base64';
                                $mail->addAddress($_SESSION['email'], $_SESSION['fullName']);
                                $mail->Subject = 'mediaIO -  jelszóváltoztatás';
                                try {
                                    $mail->send();
                                } catch (Exception $e) {
                                    echo "Mailer Error: " . $mail->ErrorInfo;
                                }
                            }
                        }
                    }     
                }
            }
        header("Location: ../profile/chPwd.php?error=none");
        mysqli_close($conn);
        exit();
    }else{
        header("Location: ../index.php?submit=AccessViolation");
        exit();
    }
?>