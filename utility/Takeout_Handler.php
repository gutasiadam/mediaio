<?php
$data = json_decode(stripslashes($_POST['data']));
foreach($data as $d){
    echo $d;
}


$serverName = "localhost";
$dbUserName = "root";
$dbPassword = "umvHVAZ%";
$dbDatabase = "mediaio";


$conn = mysqli_connect($serverName, $dbUserName, $dbPassword, $dbDatabase);

if ($conn){
    die("Connection failed: ".mysqli_connect_error());
}
?>