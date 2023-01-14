<?php
//insert.php
session_start();
$serverType = parse_ini_file(realpath('../server/init.ini')); // Server type detect
    if($serverType['type']=='dev'){
      $setup = parse_ini_file(realpath('../../../mediaio-config/config.ini')); // @ Dev
    }else{
      $setup = parse_ini_file(realpath('../../mediaio-config/config.ini')); // @ Production
    }
$n=0;
function renderUsersDraggable(){
  global $setup;
  $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
    $result = $conn->query("SELECT usernameUsers FROM users");
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $n++;
            echo '<h5><span id="user'.$n.'" class="badge badge-secondary" draggable="true" ondragstart="drag(event)">'.$row['usernameUsers'].'</span></h5>';
    }}
    $conn->close();
}

function renderWorkTable($selectedUser){
  global $setup;
  $today=date("Y/m/d");
    //Először töröljük a mainál régebbi dátumú feladokat, ha van ilyen.
    $conn = new mysqli($setup['dbserverName'], $setup['dbUserName'], $setup['dbPassword'], $setup['dbDatabase']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
    

    $conn->query("DELETE FROM feladatok WHERE Datum<'$today'");
    if ($selectedUser!="*"){
 
    $result = $conn->query("SELECT * FROM feladatok WHERE Szemely='$selectedUser' ORDER BY Datum ");
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $Datum=$row['Datum'];
        $Szemely=$row['Szemely'];
        $Feladat=$row['Feladat'];
        $resultItems[] = array('datum'=> $Datum, 'szemely'=> $Szemely, 'feladat'=>$Feladat);
      }
      $sentBack_Result=array();
      $sentBack_Result=array('message'=> 'success', 'data'=>$resultItems);
      echo(json_encode($sentBack_Result));
      exit();
      $conn->close();
    }else{
      echo "400\n";}
    }else{
      $user=$_POST["user"];
      $result = $conn->query("SELECT * FROM feladatok ORDER BY Datum ");
    }

 if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo'<tr>
        <td>'.$row['Datum'].'</td>
        <td>'.$row['Szemely'].'</td>
        <td>'.$row['Feladat'].'</td>
      </tr>';
    }
    $conn->close();
    //Összes felhasználó hozzáadása a DRAGnDROPhoz
  }
$connect=null;}

if(isset($_POST['mode']) & $_POST['mode']=='UserFiltered'){
  renderWorkTable($_SESSION['UserUserName']);
 
}


?>