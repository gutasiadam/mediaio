<?php 
    $serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
    //*ISTENÍTETT KÓD*
    if (isset($_POST['login-submit'])){
        //require 'dbHandler.ut.php';
        $conn = mysqli_connect($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
            if (!$conn){
                die("Connection failed: ".mysqli_connect_error());
            }
        $useremail = $_POST['useremail'];
        $password = $_POST['pwd'];

        //Emptycheck
        if (empty($useremail) || empty($password)){
            header("Location: ../index.php?error=emptyFields");
            exit();
        }else{
            
            $sql = "SELECT * from users WHERE usernameUsers=? OR emailUsers=?;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)){
                header('Location: ../index.php?error='.$setup['dbserverName']);
                exit();
            }else{
                mysqli_stmt_bind_param($stmt, "ss", $useremail, $useremail);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if($row = mysqli_fetch_assoc($result)){
                    $pwdcheck = password_verify($password, $row['pwdUsers']);
                    if ($pwdcheck == false){
                        header("Location: ../index.php?error=WrongPass");
                        exit();
                    }else if($pwdcheck == true){
                        session_start();
                        $_SESSION['userId'] = $row['idUsers'];
                        $_SESSION['UserUserName'] = $row['usernameUsers'];
                        $_SESSION['firstName'] = $row['firstName'];
                        $_SESSION['email']= $row['emailUsers'];
                        $_SESSION['lastName'] = $row['lastName'];
                        $_SESSION['fullName'] = ($row['lastName']." ".$row['firstName']);
                        $_SESSION['role'] = $row['Userrole'];
                        $_SESSION['color'] = "#FFFF66";
                        $_SESSION['GCodeState'] = $row['GAUTH_SECRET'];
                        header("Location: ../index.php?login=success");

                    }else{
                        header("Location: ../index.php?error=PasswordVerifFail");
                        exit();
                    }
                }else{
                    header("Location: ../index.php?error=NoUser");
                    exit();
                }
            }
        }
    }else{
        header("Location: ../index.php?submit=AccessViolation");
        exit();
    }
?>
