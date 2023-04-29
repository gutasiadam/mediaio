<?php namespace Mediaio;
require_once __DIR__.'/../Database.php';
require_once __DIR__.'/../Core.php';
use Mediaio\Core;
use Mediaio\Database;

//Maintenance manager functions will go here

//Add/delete/render work.

$sampleValue=1;
//Render workData
if(isset($_POST['method'])){
    if($_POST['method']=='get'){
        if(($_POST['getOldTasks']=='true')){
          $sql="SELECT * FROM feladatok WHERE Datum>=DATE_SUB(CURDATE(), INTERVAL 6 MONTH) ORDER BY Datum;";
        }else{
          $sql="SELECT * FROM feladatok WHERE Datum>=DATE_SUB(CURDATE(), INTERVAL 1 DAY) ORDER BY Datum;";
        }
        
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
          $sendBack_Result=array();
          while ($row = $result->fetch_assoc()) {
            $Datum=$row['Datum'];
            $Szemely1=$row['Szemely1'];
            $Szemely2=$row['Szemely2'];
            $Szemely1_Status=$row['Szemely1_Status'];
            $Szemely2_Status=$row['Szemely2_Status'];
            $resultItems[] = array('id'=> $row['ID'],'datum'=> $Datum, 'szemely1'=> $Szemely1, 'szemely2'=>$Szemely2,'szemely2_Status'=>$Szemely2_Status,'szemely1_Status'=>$Szemely1_Status);
          }
          if($_SESSION['role']>=3){
            array_push($sendBack_Result,"Admin");
          }
          
          array_push($sendBack_Result,$resultItems);
          echo(json_encode($sendBack_Result));
          exit();
        }
    }
        if($_POST['method']=='apply'){
        $workID=$_POST['workID'];
        $sql="SELECT * FROM feladatok WHERE ID=".$workID; // Selects work
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
          while ($row = $result->fetch_assoc()) {
            //If work exists, check if user1 is still empty.
            if($row['Szemely1']==NULL){
              //Add username to User1 row
              $sql="UPDATE feladatok SET Szemely1='".$_SESSION['UserUserName']."', Szemely1_Status='N' WHERE ID=".$workID;
            }else{
              if($row['Szemely1']==$_SESSION['UserUserName']){//ketszer ne irhassa be magat ugyanaz a szemely.
                echo 201;
                exit();
              } 
            if($row['Szemely1']!=NULL && $row['Szemely2']!=NULL){//Nincs hely
                echo 202;
                exit();
              }
              //Add username to User2 row
              if($row['Szemely2']==NULL){
              //Add username to User1 row
              $sql="UPDATE feladatok SET Szemely2='".$_SESSION['UserUserName']."', Szemely2_Status='N' WHERE ID=".$workID;
            }

              
            }
          }
          $connection2=Database::runQuery_mysqli();
          $connection2->query($sql);
        }
        echo 200;
        exit();
    }

    if($_POST['method']=='modify'){
        if(!isset($_POST['status2'])){
          $_POST['status2']=NULL;
        }
        $workID=$_POST['workID'];
        $sql="UPDATE feladatok SET Szemely1_Status='".$_POST['status1']."', Szemely2_Status='".$_POST['status2']."' WHERE ID=".$workID;
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
          echo 200;
        }else{
          echo $sql;
        }
        exit();
    }

    if($_POST['method']=='add'){
        
        $sql="INSERT INTO feladatok SET Datum='".$_POST['Date']."';";
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
          echo 200;
        }else{
          echo $sql;
        }
        exit();
    }

    
    if($_POST['method']=='delete'){
        $workID=$_POST['workID'];
        $sql="DELETE FROM feladatok WHERE ID='".$workID."';";
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
          echo 200;
        }else{
          echo $sql;
        }
        exit();
    }

        if($_POST['method']=='deleteUser'){
        $workID=$_POST['workID'];
        $n=$_POST["user"];
        $sql="UPDATE feladatok SET Szemely".$n."_Status=NULL, Szemely".$n."=NULL WHERE ID=".$workID;
        $connection=Database::runQuery_mysqli();
        if ($result = $connection->query($sql)) {
          echo 200;
        }else{
          echo $sql;
        }
        exit();
    }


return;
}
?>
