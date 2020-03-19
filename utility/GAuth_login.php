<?php 


if(isset($_POST['GCode'])){
$code=$_POST['GCode'];
require_once("GoogleAuthenticator.php");
include("../translation.php");
$servername = "localhost";
    $username = "root";
    $password = $application_DATABASE_PASS;
    $dbname = "loginsystem";
    $conn = new mysqli($servername, $username, $password, $dbname);
    $uName = $_SESSION['UserUserName'];
    $sql = "SELECT GAUTH_SECRET FROM users WHERE usernameUsers = '$uName'";
    $result = $conn->query($sql);
if (!$result) {
    echo 'Could not run query: ' . mysql_error();
    exit;
}
    $row = mysqli_fetch_row($result);
    $secret = $row[0];

    
    $ga = new PHPGangsta_GoogleAuthenticator();
    $result = $ga->verifyCode($secret,$code,3);

    echo $result;
}

if(isset($_POST['GcodeatLogin'])){
    $code=$_POST['GcodeatLogin'];
    require_once("GoogleAuthenticator.php");
    include("../translation.php");
    $servername = "localhost";
        $username = "root";
        $password = $application_DATABASE_PASS;
        $dbname = "loginsystem";
        $conn = new mysqli($servername, $username, $password, $dbname);
        $uName = $_SESSION['UserUserName'];
        $sql = "SELECT GAUTH_SECRET FROM users WHERE usernameUsers = '$uName'";
        $result = $conn->query($sql);
    if (!$result) {
        echo 'Could not run query: ' . mysql_error();
        exit;
    }
        $row = mysqli_fetch_row($result);
        $secret = $row[0];
    
        
        $ga = new PHPGangsta_GoogleAuthenticator();
        $result = $ga->verifyCode($secret,$code,0);
        
        if ($result==1){
            header("Location: ../index.php?login=success");
        }else{
            header("Location: ./logout.ut.php?login=WrongAuth");
        }
    }

?>