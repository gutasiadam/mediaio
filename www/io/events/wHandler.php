<?php
//insert.php
namespace Mediaio;
session_start();
use Mediaio\Database;
require_once "../Database.php";
$connect=Database::runQuery_mysqli();

if(isset($_POST["wEvent"]))
{
 if(!isset($_POST['wComment'])){$wComment="";}else{$wComment=$_POST['wComment'];}
 $query = "
 INSERT INTO worksheet 
 (FullName, EventID, Worktype, Location, Link, Comment) 
 VALUES ('".$_SESSION['lastName']." ".$_SESSION['firstName']."', '".$_POST['wEvent']."', '".$_POST['wType']."', '".$_POST['wLoc']."', '".$_POST['link']."','".$wComment."');";

 //Run query
 echo $query;
$result = mysqli_query($connect, $query);
 if($result){
     echo "1";
 }
 else{

     echo "2";
 }
 exit();
}

//Edit worksheet
if (isset($_POST["uId"]) && isset($_SESSION['UserUserName'])) {
    if (!isset($_POST['uComment'])) {
      $uComment = "";
    } else {
      $uComment = $_POST['uComment'];
    }
    $connect = Database::runQuery_mysqli();
  
    $query = "
    UPDATE worksheet
    SET Comment=?, Location=?, Worktype=?, Link=?
    WHERE ID=?";
  
    //bind params to query
    $stmt = $connect->prepare($query);
  
    //var data= {uType: uType,uLoc: uLoc,uComment: uComment,uId: uId,linkUrl: linkUrl}
    $stmt->bind_param("sssss", $uComment, $_POST['uLoc'], $_POST['uType'], $_POST['linkUrl'], $_POST['uId']);
  
  
    $result = $stmt->execute();
  
    var_dump($stmt);
  
    var_dump($result);
  
    if ($result) {
      echo "1";
    } else {
      echo "2";
    }
    exit();
  }


//Delete worksheet

if (isset($_POST["deleteId"])) {
    $WorkId = $_POST["deleteId"];
    $query = "DELETE from worksheet WHERE ID='$WorkId'";
    $connect = Database::runQuery($query);
    exit();
  }


?>