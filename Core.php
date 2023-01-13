<?php
namespace Mediaio;
require 'Database.php';
use Mediaio\Database;


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
}

?>
