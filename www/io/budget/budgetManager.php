<?php
require_once __DIR__.'/../Database.php';
use Mediaio\Database;
session_start();

//Table (0: Media, 1: Egyesulet)

if(isset($_POST['type'])){
  $connect = Database::runQuery_mysqli();
  $_POST['table']=='media'?$tableID=0:$tableID=1;
  if ($_POST['type'] == 'year') {
    //Extract data grouped by months on the specific year
        $query='
        SELECT
        EXTRACT(YEAR FROM CURRENT_TIMESTAMP) AS year,
        EXTRACT(MONTH FROM Date) AS month,
        SUM(Value) AS total_value
    FROM
        budget
    WHERE
        EXTRACT(YEAR FROM Date) = "'.$_POST['value'].'" AND TableID='.$tableID.'
    GROUP BY
        EXTRACT(YEAR FROM CURRENT_TIMESTAMP),
        EXTRACT(MONTH FROM Date)
    ORDER BY
        year, month;';
    $result = $connect->query($query);
    $data = array();
    foreach ($result as $row) {
      $data[] = $row;
    }
    echo json_encode($data);
    $connect->close();
    exit();
  }
  if ($_POST['type'] == 'month') {
    //Extract data grouped by days on the specific month
        $query='
        SELECT
        ID,
        EXTRACT(DAY FROM CURRENT_TIMESTAMP) AS day,
        Date,
        Value,
        Name,
        Data
    FROM
        budget
    WHERE
        EXTRACT(YEAR FROM Date) = "'.$_POST['value'].'" AND TableID='.$tableID.' AND EXTRACT(MONTH FROM Date) = "'.$_POST['value2'].'"
    ORDER BY
        day;';
    $result = $connect->query($query);
    $data = array();
    foreach ($result as $row) {
      $data[] = $row;
    }
    echo json_encode($data);
    $connect->close();
    exit();
  }
  if ($_POST['type'] == 'delete') {
    $query='DELETE FROM budget WHERE ID='.$_POST['id'];
    $result = $connect->query($query);
    //Check if delete succeeded
    if($result){
      echo 200;
    }else{
      echo 500;
    }
    $connect->close();
    exit();
  }

    if ($_POST['type'] == 'add') {
      $_POST['table']=='media'?$tableID=0:$tableID=1;
      $json=array();
      //Add session username to json
      $json['username']=$_SESSION['UserUserName'];
      //Add comment to json
      $json['comment']=$_POST['comment'];
      //Add json to string
      $json=mysqli_real_escape_string($connect,json_encode($json));
      
    $query="INSERT INTO `budget` (`ID`, `TableID`, `Date`, `Value`, `Name`, `Data`) VALUES 
    (NULL, ".$tableID.", '".$_POST['date']."' , ".$_POST['value'].", '".$_POST['name']."','".$json."');";
    $result = $connect->query($query);
    //Check if add succeeded
    if($result){
      echo 200;
    }else{
      echo 500;
      echo $query;
    }
    $connect->close();
    exit();
  }

  if ($_POST['type'] == 'modify') {
      $_POST['table']=='media'?$tableID=0:$tableID=1;
      $json=array();
      //Add session username to json
      $json['username']=$_SESSION['username'];
      //Add comment to json
      $json['comment']=$_POST['comment'];
      //Add json to string
      $json=mysqli_real_escape_string($connect,json_encode($json));
      

      $query="UPDATE `budget` SET `Date` = '".$_POST['date']."', `Value` = '".$_POST['value']."', `Name` = '".$_POST['name']."', `Data` = '".$json."' WHERE `budget`.`ID` = ".$_POST['id'].";";
    $result = $connect->query($query);
    //Check if add succeeded
    if($result){
      echo 200;
    }else{
      echo 500;
      echo $query;
    }
    $connect->close();
    exit();
  }

  if ($_POST['type'] == 'sum') {
    $query="SELECT TableID, SUM(Value) AS sum FROM budget GROUP BY TableID;";
    $result = $connect->query($query);
    $data = array();
    foreach ($result as $row) {
      $data[] = $row;
    }
    echo json_encode($data);
    $connect->close();
    exit();
  }
  
}


?>