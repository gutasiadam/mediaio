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
?>
