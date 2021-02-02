<?php
//insert.php
$date = date("Y-m-d");
session_start();
$connect = new PDO("mysql:host=localhost;dbname=mediaio", "root", "umvHVAZ%");
$username= $_SESSION["UserUserName"];
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
$secureId = generateRandomString();
$query = "SELECT secureId FROM eventrep WHERE secureId = '$secureId' ";
$statement = $connect->prepare($query);
$statement->execute();
if ($statement->rowCount() > 0) {
    while($statement->rowCount() == 0){
    $secureId = generateRandomString();
    $statement = $connect->prepare($query);
    $statement->execute();
}}
if(isset($_POST["title"]))
{
 $query = "
 INSERT INTO eventprep 
 (title, date_Created, start_event, end_event, borderColor, secureId, user) 
 VALUES (:title, :created, :start_event, :end_event, :borderColor, :secureId, :user)
 ";
 $statement = $connect->prepare($query);
 /*echo($connect->errorInfo());
 if (!$statement) {
    echo($connect->errorInfo());
    echo "\nPDO::errorInfo():\n";
    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    fwrite($myfile, $connect->errorInfo());
    fclose($myfile);


}*/

 $statement->execute(
  array(
   ':title'  => $_POST['title'],
   ':created' => $date,
   ':start_event' => $_POST['start'],
   ':end_event' => $_POST['end'],
   ':borderColor' => $_POST['type'],
   ':secureId' => $secureId,
   ':user' => $_SESSION['UserUserName'],
   
  )
 );


 echo "1";//E-mail elküldése a SESSION UserName felé
 include "./prep_mailer.php";

}else{
    echo "0";
}
$connect=null;


?>