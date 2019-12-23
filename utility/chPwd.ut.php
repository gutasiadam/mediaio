<?php 
    session_start();
    if (isset($_POST['pwdCh-submit'])){
        require 'dbHandler.ut.php';

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
                header("Location: ../signup.php?error=SQLError1");
                exit();
            }else{
                mysqli_stmt_bind_param($stmt, "s", $ususname);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if($row = mysqli_fetch_assoc($result)){
                    $pwdcheck = password_verify($oldpwd, $row['pwdUsers']);


                    if ($pwdcheck == false){
                        header("Location: ../profile/chPwd.php?error=OldPwdError");


                    }else if ($pwdcheck == true){
                        $hashedpwd = password_hash($password, PASSWORD_BCRYPT);


                        $sql = "UPDATE users SET pwdUsers='$hashedpwd' WHERE idUsers='$username' ;";}

                            if (!mysqli_query($conn,$sql))
                            {
                            echo("Error description: " . mysqli_error($conn));
                            }else{
                                $to = $email;

// Subject
$subject = 'MediaIO - Jelszava megváltozott';

// Message
$message = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$_SESSION["firstName"].'!</h3><p>
 Ezúton értesítjük, hogy a(z) <strong>'.$_SESSION["UserUserName"].'</strong> fiókjához tartozó jelszó megváltozott.</p>
  <h6>Ez egy automatikus üzenet. Kérjük ne válaszoljon rá.<br>Üdvözlettel: <br> Arpad Media Admin</h6>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

$headers[] = 'Bcc: gutasi.guti@gmail.com';
// Mail it
mail($to, $subject, $message, implode("\r\n", $headers));
                                header("Location: ../profile/chPwd.php?error=none");
                                exit();
                            }

                    }     
                }
            }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit();
    }else{
        header("Location: ../index.php?submit=AccessViolation");
        exit();
    }
?>