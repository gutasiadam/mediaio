<?php 
    //*ISTENÍTETT KÓD*
    if (isset($_POST['signup-submit'])){
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "loginsystem";


$conn = mysqli_connect($serverName, $dbUserName, $dbPassword, $dbDatabase);

if (!$conn){
    die("Connection failed: ".mysqli_connect_error());
}

        $lastname = $_POST['lastName'];
        $firstname = $_POST['firstName'];
        $telenum = $_POST['tele'];
        $username = $_POST['userid'];
        $email = $_POST['email'];
        $password = $_POST['pwd'];
        $passwordrepeat = $_POST['pwd-Re'];

        //Hibakezelés

        if(empty($username) || empty($email) || empty($password) || empty($passwordrepeat) || empty($firstname) || empty($telenum) || empty($lastname)){
            header("Location: ../signup.php?error=emptyField&userid=".$username."&email=".$email);
            exit();
        }else if (!filter_var($email, FILTER_VALIDATE_EMAIL) && (!preg_match("/^[a-zA-Z0-9]*$/", $username))){
            header("Location: ../signup.php?error=invalidMailUserName");
            exit();
        }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            header("Location: ../signup.php?error=invalidMail&userid=".$username);
            exit();
        }else if (!preg_match("/^[a-zA-Z0-9]*$/", $username)){
            header("Location: ../signup.php?error=invalidUserName&email=".$email);
            exit();
        }else if ($password !== $passwordrepeat){
            header("Location: ../signup.php?error=PasswordCheck&userid=".$username."&email=".$email);
            exit();
        }else if(strlen($password) < 8 ){
            header("Location: ../signup.php?error=PasswordLenght&userid=".$username."&email=".$email);
            exit();
        }else{
            //Check if this user already exists or not

            $sql = "SELECT usernameUsers FROM users WHERE usernameUsers=? /*AND pwdUsers=?*/";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: ../signup.php?error=SQLError");
                exit();
            }else{
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                $resultCheck = mysqli_stmt_num_rows($stmt);
                if ($resultCheck > 0){
                    header("Location: ../signup.php?error=UserTaken&email=".$email);
                    exit();
                }else{
                    $sql = "INSERT INTO users (usernameUsers, emailUsers, pwdUsers, firstName, lastName, teleNum) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)){
                        header("Location: ../signup.php?error=SQLError");
                        exit();
                    }else{
                        $hashedpwd = password_hash($password, PASSWORD_BCRYPT);

                        mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $hashedpwd, $firstname, $lastname, $telenum);
                        mysqli_stmt_execute($stmt);
                        header("Location: ../index.php?signup=success");
                        echo "<h1>Login succesul! Transferring to Homepage...</h1>";
                        exit();
                    }
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }else{
        header("Location: ../index.php?submit=AccessViolation");
        exit();
    }
?>