<?php
session_start();
$currDate= date("Y/m/d H:i:s");
$continue=FALSE;
$SESSuserName = $_SESSION['UserUserName'];
$data = json_decode(stripslashes($_POST['data']));
foreach($data as $d){
    echo $d;

    //Assuming that query is valid. Begin procedure.


  // Database init 
  $serverName="localhost";
  $userName="root";
  $password="umvHVAZ%";
  $dbName="mediaio";
  $countOfRec=0;

  $conn = new mysqli($serverName, $userName, $password, $dbName);

  if ($conn->connect_error) {
      die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
  }
  $sql = "SELECT Status FROM leltar WHERE Nev='$d'";
  $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
      if ($row['Status']=='0'){ // A tárgy nincs a raktárban
          $continue=TRUE;
      }else{
          echo ("400");
      }
  }


  //Prepare retrieve procedure.
  if ($continue){
    $sql = ("UPDATE `leltar` SET `Status` = '1', `RentBy` = NULL, `AuthState` = NULL WHERE `leltar`.`Nev` = '$d';");
    $sql.= ("DELETE FROM authcodedb WHERE Item = '$d';");
    $sql.= ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '1', '$currDate', '$SESSuserName', '$d', 'IN')");
    if (!$conn->multi_query($sql)) {
      echo "Multi query fail!: (" . $conn->errno . ") " . $conn->error;
    }else{
      echo "200";
      
    }
  }
  

$conn->close();
exit;
}

/*


AUTHCODE



// IF EVERYTHING IS GOOD, WRITE TO DB
if( isset($_POST['data'])){
$data = json_decode(($_POST['data']), true);
$dbName="mediaio";
foreach ($data as $d){
  $conn = new mysqli($serverName, $userName, $password, $dbName);
  $currDate = date("Y/m/d H:i:s");
  if ($conn->connect_error) {
  die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
}
else{  
  $sql = ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '1', '$currDate', '$SESSuserName', '$d', 'IN')");
  $result = $conn->query($sql);
  
  if ($result === TRUE) {
    $conn = new mysqli($serverName, $userName, $password, 'mediaio');
    $sql2 = ("UPDATE `leltar` SET `Status` = '1', `RentBy` = NULL, `AuthState` = NULL WHERE `Nev`='$d';");
    $sql2.= ("DELETE FROM authcodedb WHERE Item = '$d'");
    if (!$conn->multi_query($sql2)) {
      echo "Multi query fail!: (" . $conn->errno . ") " . $conn->error;
    }else{
      echo "Success.";
    }
    $conn->close();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
  }
echo 1;
}

exit;
}*/

?>