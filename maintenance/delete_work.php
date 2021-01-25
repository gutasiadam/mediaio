<?php
//insert.php
session_start();
if(isset($_POST["date"]) && isset($_POST["user"]) && isset($_POST["task"]))
{
    //Először nézzük meg, létezik-e a felhasználó:
    $conn = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
    $date=$_POST['date'];
    $user=$_POST["user"];
    $taskName=$_POST['task'];
    $result = $conn->query("DELETE FROM feladatok WHERE (Datum='$date' AND Szemely='$user' AND Feladat='$taskName') ");

 echo "200";//Sikeres

$connect=null;
}else{
    echo "500";//Üres cella, vagy formátumhiba.
}

?>