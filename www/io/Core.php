<?php
namespace Mediaio;
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once 'Database.php';
require_once 'Mailer.php';
require_once 'Accounting.php';
use Mediaio\Database;
use Mediaio\Accounting;
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

    //Validate user with api key
    function loginWithApikey($apikey){
        $sql = "SELECT * from users WHERE apikey='$apikey';";
        $result = Database::runQuery($sql);
        if($row = mysqli_fetch_assoc($result)){
            $userDataArray=array();
            $userDataArray['userId'] = $row['idUsers'];
            $userDataArray['username'] = $row['usernameUsers']; //Bevare! usernameUsers field has different name in the RESTAPI mode!
            $userDataArray['firstName'] = $row['firstName'];
            // $userDataArray['email']= $row['emailUsers'];
            // $userDataArray['lastName'] = $row['lastName'];
            // $userDataArray['fullName'] = ($row['lastName']." ".$row['firstName']);
            $userDataArray['role'] = $row['Userrole'];
            $userDataArray['color'] = "#FFFF66";
            $userDataArray['AdditionalData']=json_decode($row['AdditionalData'], true, JSON_UNESCAPED_SLASHES);
            // $userDataArray['groups'] = $additionalData['groups'];
            return array('code' => 200, 'userData' => $userDataArray);
        }else{
            return array('code' => 401);
        }
    }

    //Destroy api key
    function destroyApiKey($apikey){
        $sql = "UPDATE users SET APIKey=NULL WHERE APIKey=?;";
        $connection = Database::runQuery_mysqli();
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            return array('code' => '500');
        }else{
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("s", $apikey);
            //echo binded statement
            $stmt->execute();
            
            // Check affected rows on the prepared statement
            if ($stmt->affected_rows == 1) {
                $stmt->close();
                return array('code' => '200');
            }else{
                $stmt->close();
                return array('code' => '401');
            }
            $stmt->close();
            return array('code' => '418');
        }

    }

    function loginUser($postData, $RESTAPImode=false){
        //read the loginPageSettings.json file
        //var_dump($postData);
        $file = fopen(__DIR__."/data/loginPageSettings.json", "r");
        $message = fread($file, filesize(__DIR__."/data/loginPageSettings.json"));
        $message = json_decode($message, true);
        //check if the limit is set

        fclose($file);

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
                                if($RESTAPImode==true){
                                    //echo "Wrong pass";
                                    return array('code' => 401);
                                    exit();
                                }

                                Accounting::logLoginAttempt($row['idUsers'],"login_WrongPass");
                                header("Location: ../index.php?error=WrongPass");
                                exit();
                            }else if($pwdcheck == true){
                                //check if session role json contains admin or szsadmin
                                $role = json_decode($row['AdditionalData'], true);
                                //var_dump($role);
                                if($message["limit"] == 'true'){
                                    if(!in_array("admin", $role['groups']) || !in_array("system", $role['groups'])){
                                        session_unset();
                                        session_destroy();
                                        header("Location: ../index.php?error=loginLimit");
                                        exit();
                                    }
                                }

                                if($RESTAPImode==true){
                                    //echo "OK";
                                    //Generate a 2048 character long, base64 encoded token.
                                    $token = base64_encode(openssl_random_pseudo_bytes(128));
                                    //Store key in database
                                    $sql = "UPDATE users SET apikey='$token' WHERE usernameUsers='$userName' OR emailUsers='$userName';";
                                    $conn = Database::runQuery_mysqli();
                                    $result = mysqli_query($conn,$sql);
                                    //echo $sql;
                                    //Check affected rows
                                    if($conn->affected_rows!=1){
                                        return array('code' => 500);
                                    }else{
                                        return array('token' => $token, 'code' => 200);
                                    }

                                    //bind parameters
                                    

                                    exit();
                                }

                                $_SESSION['userId'] = $row['idUsers'];
                                $_SESSION['UserUserName'] = $row['usernameUsers'];
                                $_SESSION['firstName'] = $row['firstName'];
                                $_SESSION['email']= $row['emailUsers'];
                                $_SESSION['lastName'] = $row['lastName'];
                                $_SESSION['fullName'] = ($row['lastName']." ".$row['firstName']);
                                $_SESSION['role'] = $row['Userrole'];
                                $_SESSION['color'] = "#FFFF66";
                                $_SESSION['AdditionalData']=$row['AdditionalData'];
                                $additionalData = json_decode($_SESSION['AdditionalData'], true);
                                $_SESSION['groups'] = $additionalData['groups'];

                                Accounting::logEvent($row['idUsers'],"login_Success");
                                header("Location: ../index.php?login=success");
        
                            }else{
                                header("Location: ../index.php?error=PasswordVerifFail");
                                exit();
                            }
                        }else{

                            Accounting::logEvent(0,"login_NoUser");
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
        Accounting::logEvent($_SESSION['userId'],"logout");
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
                                //E-mail küldése a felhasználónak
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
                                } catch (\Exception $e) {
                                    echo "Mailer Error: " . $e;
                                }
                            
                        }
                    }     
            }
    }
    function changeRole($postData){
        $sql="SELECT AdditionalData FROM users WHERE userNameUsers='".$postData['userName']."';";
        $connection = Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $additionalData = $row['AdditionalData'];
            }
        }
        $additionalData = json_decode($additionalData, true);
        // var_dump($additionalData);
        $groups=$additionalData['groups'];
        //Default group
        if (!isset($_POST['eventCheckbox']) && !isset($_POST['teacherCheckbox']) && !isset($_POST['studioCheckbox']) && !isset($_POST['adminCheckbox']) && !isset($_POST['mediaCheckbox'])){
            $groups=array();
            //array_push($groups, "default");
        }else{
                        //if postdata adminchecked is true, add admin to groups, else remove it, if it exists  
            if (isset($_POST['mediaCheckbox'])){
                $valueExists=false;
                foreach ($groups as $key => $val){
                        if ($val == 'media'){
                           $valueExists=true;
                        }
                    }
                if ($valueExists==false){
                    array_push($groups, "média");
                }
            }else{
                //Event checked is false, remove Event from groups
                    foreach ($groups as $key => $val){
                        if ($val == 'media'){
                           unset($groups[$key]);}
                    }
            }
            if ($postData["adminChecked"]==true){
                $valueExists=false;
                foreach ($groups as $key => $val){
                        if ($val == 'admin'){
                           $valueExists=true;
                        }
                    }
                if ($valueExists==false){
                    array_push($groups, "admin");
                }
            }else{
                //Admin checked is false, remove admin from groups
                    foreach ($groups as $key => $val){
                        if ($val == 'admin'){
                           unset($groups[$key]);}
                    }
            }
            //if postdata studiochecked is true, add studio to groups, else remove it, if it exists
            if (isset($_POST['studioCheckbox'])){
                $valueExists=false;
                foreach ($groups as $key => $val){
                        if ($val == 'studio'){
                           $valueExists=true;
                        }
                    }
                if ($valueExists==false){
                    array_push($groups, "studio");
                }
            }else{
                //Admin checked is false, remove studio from groups
                    foreach ($groups as $key => $val){
                        if ($val == 'studio'){
                           unset($groups[$key]);}
                    }
            }
        
            //if postdata teacherChecked is true, add studio to groups, else remove it, if it exists
            if (isset($_POST['teacherCheckbox'])){
                $valueExists=false;
                foreach ($groups as $key => $val){
                        if ($val == 'teacher'){
                           $valueExists=true;
                        }
                    }
                if ($valueExists==false){
                    array_push($groups, "teacher");
                }
            }else{
                //Teacher checked is false, remove Teacher from groups
                    foreach ($groups as $key => $val){
                        if ($val == 'teacher'){
                           unset($groups[$key]);}
                    }
            }
        
            //if postdata eventChecked is true, add studio to groups, else remove it, if it exists
            if (isset($_POST['eventCheckbox'])){
                $valueExists=false;
                foreach ($groups as $key => $val){
                        if ($val == 'event'){
                           $valueExists=true;
                        }
                    }
                if ($valueExists==false){
                    array_push($groups, "event");
                }
            }else{
                //Event checked is false, remove Event from groups
                    foreach ($groups as $key => $val){
                        if ($val == 'event'){
                           unset($groups[$key]);}
                    }
            }

        }


        //update additionalData groups field with the new groups array
        $additionalData['groups']=$groups;
        $additionalData=json_encode($additionalData, JSON_UNESCAPED_UNICODE);
        
        $sql = "UPDATE users SET AdditionalData='$additionalData' WHERE userNameUsers='".$postData['userName']."';";
        
        return Database::runQuery($sql);
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
            $result=mysqli_query($connection,$sql);
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
    $c=new Core;
    $c->changePassword($postData);
}
if (isset($_POST['pointUpdate'])){
    $postData=array('userName'=>$_POST['userName'],'adminChecked'=>false, 
    'studioChecked'=>false,'teacherChecked'=>false);
    if (isset($_POST["adminCheckbox"])){$postData['adminChecked']=true;}
    if (isset($_POST["studioCheckbox"])){$postData['studioChecked']=true;}
    if (isset($_POST["teacherCheckbox"])){$postData['teacherChecked']=true;}
    $c = new Core();
    $c->changeRole($postData);
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
    $core=new Core;
    $core->registerUser($postData);
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
                        header("Location: ./profile/lostPwd.php?error=userData");
                        exit();
                    }
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
                    header("Location: ./profile/lostPwd.php?error=tokenSent");
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
        //echo $numRows;
        header("Location: ./profile/lostPwd.php?error=tokenError");
        exit();
    }else{
        while($row = $result->fetch_assoc()) {
            $hashedpwd = password_hash($_POST['chPwd-1'], PASSWORD_BCRYPT); 
            $sql = "UPDATE users SET pwdUsers='".$hashedpwd."', token=NULL WHERE usernameUsers='".$_POST['userName']."';";
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
                                } catch (\Exception $e) {
                                    echo "Mailer Error: " . $e;
                                }
        }
    }

        
 }

?>
