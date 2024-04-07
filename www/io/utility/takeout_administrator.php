<?php

namespace Mediaio;
error_reporting(E_ERROR | E_PARSE);
session_start();
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Core.php';

use Mediaio\Core;
use Mediaio\Database;


$SESSuserName = $_SESSION['UserUserName'];

if (isset($_POST['takeoutData'])) {
  $logDump = fopen("log.txt", "w");
  $takeoutData = ($_POST['takeoutData']['items']);
  //var_dump($takeoutData);

  //logging
  /*foreach ($takeoutData as $entry){
      //echo($entry['name']);
      fwrite($logDump, $entry['name']."\n");
      //echo($entry['id']);
      fwrite($logDump, $entry['id']."\n");
    }*/

  $currDate = date("Y/m/d H:i:s");
  $mysqli = Database::runQuery_mysqli();


  //Logging into takelog

  //Auto accept available
  if (in_array("admin", $_SESSION['groups'])) {
    if ($_POST['takeoutAsUser'] != NULL) {
      //If another person was selected at takeout, use that person's name
      $takeOutAsUser = $_POST['takeoutAsUser'];
      $sql = ("INSERT INTO takelog (`ID`, `Date`, `User`, `Items`, `Event`,`Acknowledged`,`ACKBY`) VALUES (NULL, '$currDate', '$takeOutAsUser', '" . json_encode($takeoutData) . "', 'OUT',1,'$SESSuserName')");
    } else {
      $sql = ("INSERT INTO takelog (`ID`, `Date`, `User`, `Items`, `Event`,`Acknowledged`,`ACKBY`) VALUES (NULL, '$currDate', '$SESSuserName', '" . json_encode($takeoutData) . "', 'OUT',1,'$SESSuserName')");
    }
  } else {

    //If another person was selected at takeout, use that person's name
    if ($_POST['takeoutAsUser'] != NULL) {
      $takeOutAsUser = $_POST['takeoutAsUser'];
      $sql = ("INSERT INTO takelog (`ID`, `Date`, `User`, `Items`, `Event`,`Acknowledged`,`ACKBY`) VALUES (NULL, '$currDate', '$takeOutAsUser', '" . json_encode($takeoutData) . "', 'OUT',0,NULL)");
    } else {
      $sql = ("INSERT INTO takelog (`ID`, `Date`, `User`, `Items`, `Event`,`Acknowledged`,`ACKBY`) VALUES (NULL, '$currDate', '$SESSuserName', '" . json_encode($takeoutData) . "', 'OUT',0,NULL)");
    }
  }
  $result = mysqli_query($mysqli, $sql);
  //If logging succeeded
  if ($result == TRUE) {
    //Change every item as taken in the database

    //If another person was selected at takeout, use that person's name
    if ($_POST['takeoutAsUser'] != NULL) {
      $SESSuserName = $_POST['takeoutAsUser'];
    }
    foreach ($takeoutData as $i) {
      $name = $i["name"]; //Obsolete!
      $uid = $i["uid"];
      if (in_array("admin", $_SESSION['groups'])) { //Auto accept complete, automatically update databse
        //0 : unavailable
        $sql = ("UPDATE leltar SET Status = 0, RentBy = '$SESSuserName' WHERE `UID`='$uid'");
        //echo "Auto accept OK, sql: ".$sql."\n";
      } else { //Manual accept, update database with status 2
        $sql = ("UPDATE leltar SET Status = 2, RentBy = '$SESSuserName' WHERE `UID`='$uid'");
      }
      $result = mysqli_query($mysqli, $sql);
      if ($result != TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }
    echo 200;
    $mysqli->close();
    exit();
  } else {
    echo 400;
    $mysqli->close();
    exit();
  }

  // }
  //Reloads items
  include('./refetchdata.php');
  exit;
} else {
  echo 500;
  exit();
}
