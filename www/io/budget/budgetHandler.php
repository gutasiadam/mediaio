<?php
require_once __DIR__.'/../Database.php';
use Mediaio\Database;
session_start();

$connect = Database::runQuery_mysqli();
$Dates = preg_split("#/#", $_POST['bDate']); 
if(isset($_POST["bVal"]))
{
 $query="INSERT INTO `main_budget` (`Author`, `Type`, `Description`, `Amount`, `Year`, `Month`, `Day`, `budget_type`, `addedBy`)
  VALUES ('".$_POST['bUser']."', '".$_POST['bType']."', '".$_POST['bName']."', '".$_POST['bVal']."', ".$Dates[0].", ".$Dates[1].", ".$Dates[2].", '".$_POST['bKassza']."', 
  '".$_POST['bUser']."')";

    $connect->query($query);
 $connect->close();
 echo "1";
}
?>