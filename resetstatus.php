<?php
session_start();
if ($_SESSION['role']=="admin"){
//Resets every item that is took out.
$conn = new mysqli('localhost', 'root', 'umvHVAZ%', 'leltar_master');
$sql = ("SELECT * FROM `leltar` WHERE `Status` = 0");
$result = $conn->query($sql);
$rowReturn = $result->num_rows;
if ($rowReturn === 0){
    echo "Nincs semmi visszafordítandó.";}
else {
    $sql = ("UPDATE leltar SET `Status`= 1,`AuthState`= NULL,`RentBy`=NULL");
    $result = $conn->query($sql);
    if ($result === TRUE){
        echo "Reset kész.";
    }
    else{
        echo "Error at reset";
    }
}
$conn->close();
}
else{echo "Nincs jogod a RESETSTATUS parancs futtatásához.";}?>