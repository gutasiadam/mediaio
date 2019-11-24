<?php
$conn = new mysqli('localhost', 'root', 'umvHVAZ%', 'leltar_master');
$sql = ("SELECT * FROM `leltar` WHERE `Status` = 0");
$result = $conn->query($sql);
$rowReturn = $result->num_rows;
if ($rowReturn === 0){
    echo "There is nothing to do. Idle.";
}
else {
    $sql = ("UPDATE leltar SET `Status`= 1,`AuthState`= NULL,`RentBy`=NULL");
    $result = $conn->query($sql);
    if ($result === TRUE){
        echo "Reset complete.";
    }
    else{
        echo "Error at reset";
    }
}
$conn->close();

?>