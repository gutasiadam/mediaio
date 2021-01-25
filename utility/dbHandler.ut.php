<?php 

$serverName = "localhost";
$dbUserName = "root";
$dbPassword = "umvHVAZ%";
$dbDatabase = "mediaio";


$conn = mysqli_connect($serverName, $dbUserName, $dbPassword, $dbDatabase);

if (!$conn){
    die("Connection failed: ".mysqli_connect_error());
}
?>