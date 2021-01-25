<link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet' />
<?php 
    //*ISTENÍTETT KÓD*
    if (isset($_POST['signup-submit'])){
        $serverName = "localhost";
        $dbUserName = "root";
        $dbPassword = "umvHVAZ%";
        $dbDatabase = "mediaio";


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
        $role = "Default";

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
                    $sql = "INSERT INTO users (usernameUsers, emailUsers, pwdUsers, firstName, lastName, teleNum, Userrole) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)){
                        header("Location: ../signup.php?error=SQLError");
                        exit();
                    }else{
                        $hashedpwd = password_hash($password, PASSWORD_BCRYPT);

                        mysqli_stmt_bind_param($stmt, "sssssss", $username, $email, $hashedpwd, $firstname, $lastname, $telenum, $role);
                        mysqli_stmt_execute($stmt);
$to = $email;

// Subject
$subject = 'MediaIO - Regisztráció';

// Message
$message = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$firstname.'!</h3><p>
 Köszönjük, hogy regisztráltál az <strong>Arpad Media IO</strong> rendszerünkben!</p>
 Az adataid a következők: 
 <table>
    <tr>
      <th>Teljes Név</th><th>Felhasználónév</th><th>E-mail cím</th><th>Telefonszám</th>
    </tr>
    <tr>
      <td>'.$lastname.' '.$firstname.'</td><td>'.$username.'</td><td>'.$email.'</td><td>'.$telenum.'</td>
    </tr>
  </table>
  <h6>Ez egy automatikus üzenet. Kérem ne küldjön vissza semmit.<br>Üdvözlettel: <br> Arpad Media Admin</h6>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';

/* Additional headers
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Birthday Reminder <birthday@example.com>';
$headers[] = 'Cc: birthdayarchive@example.com';*/
$headers[] = 'Bcc: gutasi.guti@gmail.com';
// Mail it
mail($to, $subject, $message, implode("\r\n", $headers));
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