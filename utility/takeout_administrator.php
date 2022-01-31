<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE );
session_start();
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
$SESSuserName = $_SESSION['UserUserName'];

if( isset($_POST['takeoutData'])){
  $logDump=fopen("log.txt", "w");
  $takeoutData= ($_POST['takeoutData']['items']);
    //var_dump($data['items']);
    
    foreach ($takeoutData as $entry){
      //echo($entry['name']);
      fwrite($logDump, $entry['name']."\n");
      //echo($entry['id']);
      fwrite($logDump, $entry['id']."\n");
    }
    //file_put_contents("dump.txt", ob_get_flush());
    
    foreach ($takeoutData as $i){
        fwrite($logDump, "RUN"."\n");
        $nev= $i["name"];
        $id=number_format($i["id"]);
        if ($id<1000){
        $currDate = date("Y/m/d H:i:s");
        $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
        //$conn = new mysqli($serverName, $dbUserName, $dbPassword, $dbDatabase);
    if ($conn->connect_error) {
      die("Connection fail: (Is the DB server maybe down?)" . $conn->connect_error);
    }
    else{  
      $sql = ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '$id', '$currDate', '$SESSuserName', '$nev', 'OUT')");
      $result = $conn->query($sql);
      $conn->close();
      if ($result === TRUE) {
        $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
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
    }
    
  }
  //Reloads items
  include('./refetchdata.php');
  exit;
}
?>