<?php
//insert.php
session_start();
$connect = new PDO("mysql:host=localhost;dbname=budget", "root", "umvHVAZ%");

if(isset($_POST["bVal"]))
{
 $query="INSERT INTO `main_budget` (`Author`, `Type`, `Description`, `Amount`)
  VALUES (:author, :typee, :descriptionn, :amount)";
 $statement = $connect->prepare($query);
 $statement->execute(
  array(
   ':author' => $_POST['bUser'],
   ':typee'  => $_POST['bType'],
   ':descriptionn' => $_POST['bName'],
   ':amount' => $_POST['bVal']
   
  )
 );
 $connect=null;
 echo "1";
}
?>