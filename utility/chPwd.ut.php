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