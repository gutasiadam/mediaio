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
 (FullName, EventID, Worktype, Location, Comment) 
 VALUES ('".$_SESSION['lastName']." ".$_SESSION['firstName']."', '".$_POST['wEvent']."', '".$_POST['wType']."', '".$_POST['wLoc']."','".$wComment."');";

 //Run query
 //echo $query;
$result = mysqli_query($connect, $query);
 if($result){
     echo "1";
 }
 else{
     echo "2";
 }
}


?>