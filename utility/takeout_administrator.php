<?php
session_start();
$serverName = "localhost";
$dbUserName = "root";
$dbPassword = "umvHVAZ%";
$dbDatabase = "mediaio";
$SESSuserName = $_SESSION['UserUserName'];

if( isset($_POST['takeoutData'])){
  $logDump=fopen("log.txt", "w");
  $takeoutData= ($_POST['takeoutData']['items']);
    //var_dump($data['items']);
    
    foreach ($takeoutData as $entry){
      echo($entry['name']);
      fwrite($logDump, $entry['name']."\n");
      echo($entry['id']);
      fwrite($logDump, $entry['id']."\n");
    }
    //file_put_contents("dump.txt", ob_get_flush());
    
    foreach ($takeoutData as $i){
        fwrite($logDump, "RUN"."\n");
        $nev= $i["name"];
        $id=number_format($i["id"]);
        if ($id<1000){
        $currDate = date("Y/m/d H:i:s");
        $conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase);
    if ($conn->connect_error) {
      die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
    }
    else{  
      $sql = ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '$id', '$currDate', '$SESSuserName', '$nev', 'OUT')");
      $result = $conn->query($sql);
      $conn->close();
      if ($result === TRUE) {
        $conn = new mysqli($serverName, $dbUserName, $dbPassword, 'leltar_master');
        $sql2 = ("UPDATE leltar SET Status = 0, RentBy = '$SESSuserName' WHERE `Nev`='$nev'");
        $result2 = $conn->query($sql2);
        $conn->close();
        if ($result2 != TRUE){
          fwrite($logDump, "ERROR"."\n");
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
      }
    echo $nev;
    }
    
  }
  //Reloads items
  include('./refetchdata.php');
  exit;
}
?>