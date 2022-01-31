<?php
//insert.php
session_start();
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
if(isset($_POST["date"]) && isset($_POST["user"]) && isset($_POST["task"]))
{
    //Először nézzük meg, létezik-e a felhasználó:
    $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
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