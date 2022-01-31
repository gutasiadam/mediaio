<?php
session_start();
$currDate= date("Y/m/d H:i:s");
$continue=FALSE;
$SESSuserName = $_SESSION['UserUserName'];
$mode = ($_POST['mode']);
$data = json_decode(stripslashes($_POST['data']));
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
if ($mode=="handle"){ // A beérkező tárgy(ak) adminisztrálása, visszatevése.
  foreach($data as $d){
    echo $d;
    //Assuming that query is valid. Begin procedure.


  // Database init 
  $countOfRec=0;

  $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);

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
    $sql = ("UPDATE `leltar` SET `Status` = '1', `RentBy` = NULL WHERE `leltar`.`Nev` = '$d';");
    //$sql.= ("DELETE FROM authcodedb WHERE Item = '$d';");
    $sql.= ("INSERT INTO takelog (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '1', '$currDate', '$SESSuserName', '$d', 'IN')");
    if (!$conn->multi_query($sql)) {
      echo "Multi query fail!: (" . $conn->errno . ") " . $conn->error;
    }else{
      echo "200";
      
    }
  }
  

  
}
$conn->close();
  exit;
}

if ($mode=="check"){ // Egy ellenőrző karakter generálása, amiből megtudja a JScript, hogy vizuálisan hová kell tenni az adatot. a .add_btn hívja elő.
  $serverName="localhost";
  $userName="root";
  $password="umvHVAZ%";
  $dbName="mediaio";
  $countOfRec=0;
  $item = $_POST['data']; // Azért kell ide külön, mert a kód eleján megadott striplash-t nem kezeli jól itt a PHP.
  $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
  $sql = "SELECT RentBy, Status FROM leltar WHERE Nev='$item'";
  /* Lehetséges kimenetek:
  A - A felhasználó visszahoz egy önmaga által kivett tárgyat.
  B - A felhasználó egy bennlévő tárgyra hivatkozott.
  C - A felhasználó egy más által kivett tárgyra mutat.
  X - Hiba.*/
  $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
      if ($row['Status']=='0' && $row['RentBy']==$SESSuserName){ // "A" eset
        echo "A";
        exit;
      }else if ($row['Status']=='1') { // "B" eset
        echo "B";
        exit;
      }else if($row['Status']=='0' && $row['RentBy']!=$SESSuserName){// "C" eset
        echo "C";
        exit;
      }
      else{
        echo "X"; //"X" eset: Hiba.
      }
  }
}


if ($mode=="auth"){
  exit;
}

if ($mode=="test"){
  echo "TEST";
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