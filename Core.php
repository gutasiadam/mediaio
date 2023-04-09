<?php
namespace Mediaio;
require_once 'Database.php';
require_once 'Mailer.php';
use Mediaio\Database;
use Mediaio\MailService;
session_start();

class Core{
    public $userID;
    public $userName;
    public $firstName;
    public $email;
    public $lastName;
    public $fullName;
    public $role;
    public $color;


    function setUserData($userData){
        $this->$userID=$userData[0];
        $this->$userName=$userData[1];
        $this->$firstName=$userData[2];
        $this->$email=$userData[3];
        $this->$lastName=$userData[4];
        $this->$fullName=$userData[5];
        $this->$role=$userData[6];
        $this->$color=$userData[7];
    }

    function loginUser($postData){
        if (isset($postData['login-submit'])){
            $userName = $postData['useremail'];
            $password = $postData['pwd'];
    
            //Emptycheck
            if (empty($userName) || empty($password)){
                header("Location: ../index.php?error=emptyFields");
                exit();
            }else{

                $sql = "SELECT * from users WHERE usernameUsers='$userName' OR emailUsers='$userName';";
                 $result = Database::runQuery($sql);
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
        }else{
            header("Location: ../index.php?submit=AccessViolation");
            exit();
        }
    }
    function logoutUser(){
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../index.php?logout=success");
    }
    function changePassword($postData){
        if(empty($postData['oldpwd']) || empty($postData['password']) || empty($postData['passwordrepeat'])){
            header("Location: ./profile/chPwd.php?error=emptyField");
            exit();
        }else if ($postData['password'] != $postData['passwordrepeat']){
        header("Location: ./profile/chPwd.php?error=PasswordCheck");
        exit();
        }else if(strlen($postData['password']) < 8 ){
            header("Location: ./profile/chPwd.php?error=PasswordCheck");
            exit();
        }else{
            //Check if current password is correct.

                $result=Database::runQuery("SELECT * FROM users WHERE usernameUsers='".$postData['username']."';");
                if($row = mysqli_fetch_assoc($result)){
                    $pwdcheck = password_verify($postData['oldpwd'], $row['pwdUsers']);
                    if ($pwdcheck == false){
                        header("Location: ./profile/chPwd.php?error=OldPwdError");
                    }else if ($pwdcheck == true){
                        $hashedpwd = password_hash($postData['password'], PASSWORD_BCRYPT); 
                        $sql = "UPDATE users SET pwdUsers='$hashedpwd' WHERE usernameUsers='".$postData['username']."';";
                        $result=Database::runQuery($sql);
                                //E-mail küldése a felhasznßálónak
                                $content = '
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
                                try {
                                    MailService::sendContactMail('MediaIO - jelszócsere',$_SESSION['email'],'Sikeres jelszócsere!',$content);
                                    header("Location: ./profile/chPwd.php?error=none");
                                } catch (Exception $e) {
                                    echo "Mailer Error: " . $mail->ErrorInfo;
                                }
                            
                        }
                    }     
            }
    }
    function changeRole($postData){
        if ($postData["adminChecked"]==true){
            if ($postData["studioChecked"]==true){
                $SQL = ("UPDATE `users` SET `Userrole` = 3 WHERE `users`.`userNameUsers` = '".$postData['userName']."'");
            }else{
                $SQL = ("UPDATE `users` SET `Userrole` = 4 WHERE `users`.`userNameUsers` = '".$postData['userName']."'");
            }
        }else if ($postData["studioChecked"]==true){
            $SQL = ("UPDATE `users` SET `Userrole` = 2 WHERE `users`.`userNameUsers` = '".$postData['userName']."'");
          }
          if ($postData["studioChecked"]==false and $postData["adminChecked"]==false){
            $SQL = ("UPDATE `users` SET `Userrole` = 1 WHERE `users`.`userNameUsers` = '".$postData['userName']."'");
          }
          return Database::runQuery($SQL);
    }
    function registerUser($postData){
         //Hibakezelés

         if(empty($postData['username']) || empty($postData['email']) || empty($postData['password']) || empty($postData['passwordrepeat']) || empty($postData['firstname']) || empty($postData['telenum']) || empty($postData['lastname'])){
            header("Location: ./signup.php?error=emptyField&userid=".$postData['username']."&email=".$postData['email']);
            exit();
        }else if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL) && (!preg_match("/^[a-zA-Z0-9]*$/", $postData['username']))){
            header("Location: ./signup.php?error=invalidMailUserName");
            exit();
        }else if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)){
            header("Location: ./signup.php?error=invalidMail&userid=".$postData['username']);
            exit();
        }else if (!preg_match("/^[a-zA-Z0-9]*$/", $postData['username'])){
            header("Location: ./signup.php?error=invalidUserName&email=".$postData['email']);
            exit();
        }else if ($postData['password'] !== $postData['passwordrepeat']){
            header("Location: ./signup.php?error=PasswordCheck&userid=".$postData['username']."&email=".$postData['email']);
            exit();
        }else if(strlen($postData['password']) < 8 ){
            header("Location: ./signup.php?error=PasswordLenght&userid=".$postData['username']."&email=".$postData['email']);
            exit();
        }else{
            //Check if this user already exists
            $sql = "SELECT usernameUsers FROM users WHERE usernameUsers='".$postData['username']."'" /*AND pwdUsers=?*/;
            $connection=Database::runQuery_mysqli($sql); 
            $result=mysqli_query($connectionObject,$sql);
            $resultCheck = mysqli_num_rows($result);
            if ($resultCheck > 0){
                //Username already exists.
                header("Location: ../signup.php?error=UserTaken&email=".$postData['email']);
                exit();
            }else{
                //Close previous connection.
                mysqli_close($connection);

                //ready to insert into the database;
                $connection=Database::runQuery_mysqli($sql); 
                $stmt = mysqli_stmt_init($connection);
                if (!mysqli_stmt_prepare($stmt, $sql)){
                    header("Location: ../signup.php?error=SQLError");
                    exit();
                }else{
                    //Hash the password.
                    $hashedpwd = password_hash($postData['password'], PASSWORD_BCRYPT);

                    $sql = "INSERT INTO users
                    (usernameUsers, firstName, lastName, teleNum, emailUsers, pwdUsers, Userrole, UserPoints) VALUES
                    ('".$postData['username']."', '".$postData['firstname']."', '".$postData['lastname']."', '".$postData['telenum']."', '". $postData['email']."', '".$hashedpwd."', ".$postData['role'].",0.00)";
                    $connectionObject=Database::runQuery_mysqli();
                    mysqli_query($connectionObject,$sql);
                    $affectedRows = mysqli_affected_rows($connectionObject);
                    if ($affectedRows!=1){
                            header("Location: ./signup.php?error=SQLError");
                            exit();
                    }
                    mysqli_close($connection);

                    //Ready to send e-mail to user.
                    $subject = 'MediaIO - Regisztráció';
                    $message ='
                        <html>
                        <head>
                          <title>Arpad Media IO</title>
                        </head>
                        <body>
                          <h3>Kedves '.$postData['firstname'].'!</h3><p>
                         Köszönjük, hogy regisztráltál az <strong>Arpad Media IO</strong> rendszerünkben!</p>
                         Az adataid a következők:
                         <table>
                            <tr>
                              <th>Teljes Név</th><th>Felhasználónév</th><th>E-mail cím</th><th>Telefonszám</th>
                            </tr>
                            <tr>
                              <td>'.$postData['lastname'].' '.$postData['firstname'].'</td><td>'.$postData['username'].'</td><td>'.$postData['email'].'</td><td>'.$postData['telenum'].'</td>
                            </tr>
                          </table>
                          <h6>Ez egy automatikus üzenet. Kérem ne küldjön vissza semmit.<br>Üdvözlettel: <br> Arpad Media Admin</h6>
                        </body>
                        </html>';
                    MailService::sendContactMail('MediaIO',$postData['email'],'Sikeres Regisztráció',$message);
                    header("Location: ./index.php?signup=success");
                }
            }
        }
    }

    /* Generates random strings */
    function generateRandomString($length = 6) {
        return substr(str_shuffle(str_repeat($x='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    function createLostPassWordToken(){
        //Token that will be used to create the new password.
        $TOKEN = generateRandomString();
        $username = $_POST['userName'];
        $emailAddr = $_POST['emailAddr'];
        if(empty($username) || empty($emailAddr)){
            header("Location: ./profile/lostPwd.php?error=emptyField");
            exit();
        }else{
            //Check if password is correct.
            $sql = "UPDATE users SET TOKEN='$TOKEN' WHERE usernameUsers='$username' AND emailUsers='$emailAddr'";
            $connectionObject=Database::runQuery_mysqli();
            $result=mysqli_query($connectionObject,$sql);
            return $TOKEN;
            //if(mysqli_affected_rows($result)!)
        }
    }   
}

//Jelszocsere
if (isset($_POST['pwdCh-submit'])){
    $postData=array('userId'=>$_SESSION['userId'],'username'=>$_SESSION['UserUserName'],'oldpwd'=>$_POST['pwd-Old'], 
    'password'=>$_POST['pwd-New'],'passwordrepeat'=>$_POST['pwd-New-Check']);
    Core::changePassword($postData);
}
if (isset($_POST['pointUpdate'])){
    $postData=array('userName'=>$_POST['userName'],'adminChecked'=>false, 
    'studioChecked'=>false);
    if (isset($_POST["adminCheckbox"])){
        $postData['adminChecked']=true;
    }
    if (isset($_POST["studioCheckbox"])){
        $postData['studioChecked']=true;
    }

    Core::changeRole($postData);
    header("Location: ./profile/roles.php?adminChecked=".strval($_POST['adminCheckbox']."a"));
}
if(isset($_POST['register'])){
    $postData=array(
    'lastname' => $_POST['lastName'],
    'firstname' => $_POST['firstName'],
    'telenum' => $_POST['tele'],
    'username' => $_POST['userid'],
    'email' => $_POST['email'],
    'password' => $_POST['pwd'],
    'passwordrepeat' => $_POST['pwd-Re'],
    'role' => "1"
    );
    Core::registerUser($postData);
}
 if (isset($_POST['pwdLost-submit'])){
    //createLostPassWordToken();
    //Token that will be used to create the new password.
        $TOKEN = substr(str_shuffle(str_repeat($x='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(6/strlen($x)) )),1,6);
        $username = $_POST['userName'];
        $emailAddr = $_POST['emailAddr'];
        if(empty($username) || empty($emailAddr)){
            header("Location: ./profile/lostPwd.php?error=emptyField");
            exit();
        }else{
            //Check if password is correct.
            $sql = "UPDATE users SET token='$TOKEN' WHERE usernameUsers='$username' AND emailUsers='$emailAddr'";
            $connectionObject=Database::runQuery_mysqli();
            $result=mysqli_query($connectionObject,$sql);
            $affectedRows = mysqli_affected_rows($connectionObject);
                    if ($affectedRows!=1){
                        header("Location: ./profile/lostPwd..php?error=userData");
                        exit();
                    }
            //if(mysqli_affected_rows($result)!)
               //Ready to send e-mail to user.
                    $subject = 'MediaIO - Elfelejtett jelszó';
                    $message ='
                        <html>
                        <head>
                          <title>Arpad Media IO</title>
                        </head>
                        <body>
                          <h3>Kedves '.$username.'!</h3><p>
                        Jelszó visszaállítást kértél az <strong>Arpad Media IO</strong> fiókodhoz.</p>
                        A következő, egyszer használatos tokened segítségével visszaállíthatod azt:
                         <strong>'.$TOKEN.'</strong>
                          <h6>Ha ezt a tokent nem te kérted, kérlek lépj kapcsolatba egy vezetőségi taggal. Üdvözlettel: <br> Arpad Media Admin</h6>
                        </body>
                        </html>';
                    MailService::sendContactMail('MediaIO',$emailAddr,'Jelszó helyreállítási token',$message);
                    header("Location: ./profile/lostPwd.php?error=none");
        }
 }


 //Jelszo helyrallitas, csere
 if(isset($_POST['pwdLost-change-submit'])){
    //Check if token is correct.
    $sql = "SELECT * from users WHERE usernameUsers='".$_POST['userName']."' AND emailUsers='".$_POST['emailAddr']."' AND token='".$_POST['token']."'";
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connectionObject=Database::runQuery_mysqli();
    $result=mysqli_query($connectionObject,$sql);
    $numRows = mysqli_num_rows($result);
    if ($numRows!=1){
        echo $numRows;
        //header("Location: ./signup.php?error=SQLError");
        //    exit();
    }else{
        while($row = $result->fetch_assoc()) {
            $hashedpwd = password_hash($postData['chPwd-1'], PASSWORD_BCRYPT); 
            $sql = "UPDATE users SET pwdUsers='$hashedpwd' WHERE usernameUsers='".$postData['username']."';";
                        $result=Database::runQuery($sql);
                                //E-mail küldése a felhasznßálónak
                                $content = '
                                <html>
                                <head>
                                <title>Arpad Media IO</title>
                                </head>
                                <body>
                                <h3>Kedves '.$_POST['userName'].'!</h3>
                                <p>Ezúton tájékoztatunk, hogy jelszavadat sikeresen megváltoztattad!</p>

                                Ha nem te változtattad meg a jelszavadat, azonnal jelezd azt a vezetőségnek!
                                <h5>Üdvözlettel: <br> Arpad Media Admin👋</h5>
                                </body>
                                </html>
                                ';
                                try {
                                    MailService::sendContactMail('MediaIO - jelszócsere',$_POST['emailAddr'],'Sikeres jelszócsere!',$content);
                                    header("Location: ./profile/lostPwd.php?error=none");
                                } catch (Exception $e) {
                                    echo "Mailer Error: " . $mail->ErrorInfo;
                                }
        }
    }

        
 }

?>
