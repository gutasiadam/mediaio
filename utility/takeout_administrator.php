<?php
namespace Mediaio;
session_start();
require_once __DIR__.'/../Database.php';
require_once __DIR__.'/../Core.php';
use Mediaio\Core;
use Mediaio\Database;
error_reporting(E_ERROR | E_WARNING | E_PARSE );
$SESSuserName = $_SESSION['UserUserName'];

if( isset($_POST['takeoutData'])){
  $logDump=fopen("log.txt", "w");
  $takeoutData= ($_POST['takeoutData']['items']);
    //var_dump($takeoutData);
    
    foreach ($takeoutData as $entry){
      //echo($entry['name']);
      fwrite($logDump, $entry['name']."\n");
      //echo($entry['id']);
      fwrite($logDump, $entry['id']."\n");
    }
    $currDate= date("Y/m/d H:i:s");
    $mysqli = Database::runQuery_mysqli();
    if(in_array("admin",$_SESSION['groups'])){//Auto accept 
      echo "Auto accept available";
      $sql = ("INSERT INTO takelog (`ID`, `Date`, `User`, `Items`, `Event`,`Acknowledged`,`ACKBY`) VALUES (NULL, '$currDate', '$SESSuserName', '".json_encode($takeoutData)."', 'OUT',1,'$SESSuserName')");
    }else{
      $sql = ("INSERT INTO takelog takelog (`ID`, `Date`, `User`, `Items`, `Event`,`Acknowledged`,`ACKBY`) VALUES (NULL, '$currDate', '$SESSuserName', '".json_encode($takeoutData)."', 'OUT',0,NULL)");
    }
    echo "sql: ".$sql."\n";
     $result=mysqli_query($mysqli,$sql);
      if($result == TRUE){
        //Change every item as taken in the database
        echo "Change every item as taken in the database";
        foreach ($takeoutData as $i){
          $name= $i["name"];
          if(in_array("admin",$_SESSION['groups'])){//Auto accept complete, automatically update databse
            //0 : unavailable
            $sql = ("UPDATE leltar SET Status = 0, RentBy = '$SESSuserName' WHERE `Nev`='$name'");
             echo "Auto accept OK, sql: ".$sql."\n";
          }else{ //Manual accept, update database with status 2
            $sql = ("UPDATE leltar SET Status = 2, RentBy = '$SESSuserName' WHERE `Nev`='$name'");
          }
          $result=mysqli_query($mysqli,$sql);
          if($result != TRUE){
            echo "Error: " . $sql . "<br>" . $conn->error;
          }
        }
        $mysqli->close();
      }else{
        return 400;
        $mysqli->close();
        exit();
      }
    
  // }
  //Reloads items
  include('./refetchdata.php');
  exit;
}else{
  return 500;
  exit();
}
?>