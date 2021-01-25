<?php
//insert.php
session_start();
$connect = new PDO("mysql:host=localhost;dbname=mediaio", "root", "umvHVAZ%");
$Dates = preg_split("#/#", $_POST['bDate']); 
if(isset($_POST["bVal"]))
{
 $query="INSERT INTO `main_budget` (`Author`, `Type`, `Description`, `Amount`, `Year`, `Month`, `Day`)
  VALUES (:author, :typee, :descriptionn, :amount, :yearr, :moth, :dayy)";
 $statement = $connect->prepare($query);
 $statement->execute(
  array(
   ':author' => $_POST['bUser'],
   ':typee'  => $_POST['bType'],
   ':descriptionn' => $_POST['bName'],
   ':amount' => $_POST['bVal'],
   ':yearr' => $Dates[0],
   ':moth' => $Dates[1],
   'dayy' => $Dates[2]
   
  )
 );
 $connect=null;
 echo "1";
}
?>