<?php 
namespace Mediaio;
require_once __DIR__.'/Database.php';
require_once __DIR__.'/Core.php';
use Mediaio\Core;
use Mediaio\Database;


class takeOutManager{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageTakeout(){
    //Accesses post and Session Data.
  }

  //Usercheck approved takeout process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  static function approveTakeout($value){
    if($value=='true'){/*  If approved (value=true)  */
        $userName = $_SESSION['UserUserName'];
        if(empty($userName)){
          return 400; // Session data is empty (e.g User is not loggged in.)
        }else{
          $data = json_decode(stripslashes($_POST['data']));
          $dataArray=array();
          foreach($data as $d){
            //() rész kivágása a dból
            $substrings = explode(" [", $d);
            $d = $substrings[0];
          array_push($dataArray,ltrim($d));
        }
          //For Use in the SQL query.
          $dataString = "'" . implode ( "','", $dataArray ) . "'";
          //Restore Items allowing others to take it out.
          $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=0 AND RentBy='".$_POST['user']."' WHERE leltar.Nev IN (".$dataString.");";
          //Acknowledge events in log.
          $sql.="UPDATE takelog SET Acknowledged=1, ACKBY='".$userName."' WHERE User='".$_POST['user']."' AND Date='".$_POST['date']."' AND EVENT='OUT' AND Item IN (".$dataString."); COMMIT;";
      
          $connection=Database::runQuery_mysqli();
          if(!$connection->multi_query($sql)){
            printf("Error message: %s\n", $connection->error);
          }else{
            //All good, return OK message
            //echo $sql;
            echo 200;
            return;
          }
        }
    }else{
      /*  If not approved (value=false) - Decline takeout  */
        $userName = $_SESSION['UserUserName'];
        if(empty($userName)){
          return 400; // Session data is empty (e.g User is not loggged in.)
        }else{
          $data = json_decode(stripslashes($_POST['data']));
          $dataArray=array();
          foreach($data as $d){
            //() rész kivágása a dból
            $substrings = explode(" [", $d);
            $d = $substrings[0];
          array_push($dataArray,ltrim($d));
          
        }
          //echo "DataArray:".$dataArray;
          //For Use in the SQL query.
          $dataString = "'" . implode ( "','", $dataArray ) . "'";
      
          //Restore Items allowing others to take it out.
          $sql="START TRANSACTION; UPDATE leltar SET Status=1, RentBy='NULL' WHERE Nev IN (".$dataString.");";
          //Remove takeOut form  log.
          $sql.="DELETE FROM takelog WHERE Acknowledged=0 AND User='".$_POST['user']."'AND Event='OUT' AND Item IN (".$dataString."); COMMIT;";
      
          $connection=Database::runQuery_mysqli();
          if(!$connection->multi_query($sql)){
            printf("Error message: %s\n", $connection->error);
          }else{
            //All good, return OK message
            echo 200;
            return;
          }
        }     
    }

    
  }

}

class retrieveManager{
  /*
  User takeout process. Stages the takeout as it still needs to be approved on userCheck panel.
  sets the item status to 2 (needs approvement.)
  */
  static function stageRetrieve(){
    //Accesses post and Session Data.
    //CHECK if sesison data is empty!
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }
    date_default_timezone_set('Europe/Budapest');
    $currDate= date("Y/m/d H:i:s");
    $data = json_decode(stripslashes($_POST['data']));
    $dataArray=array();
    $countOfRec=0;
    //New query - reduced to single query containing all items using the implode function;
    $countOfRec+=1;
    foreach($data as $d){
            //() rész kivágása a dból
            $substrings = explode(" [", $d);
            $d = $substrings[0];
          array_push($dataArray,ltrim($d));
    }
    //For Use in SQL query.
    $dataString = "'" . implode ( "','", $dataArray ) . "'";
    // Database init  - create a mysqli object
      
      $connection=Database::runQuery_mysqli();
      if($_SESSION['role']>3){//Auto accept retrieve
        //echo "Auto accept";
              $sql=" 
      START TRANSACTION; UPDATE leltar SET leltar.Status=1, leltar.RentBy=NULL WHERE leltar.Nev IN (".$dataString.");";
      $sql.="INSERT INTO takelog VALUES";
    foreach($data as $d){
      $sql.="(NULL, 0, '$currDate', '$userName', '$d', 'IN',1,'$userName'),";
    }
    
      //Removes last comma from sql command.
      $sql=substr_replace($sql, "", -1);
      $sql.="; COMMIT;";
      //echo $sql;
        if(!$connection->multi_query($sql)){
          printf("Error message: %s\n", $connection->error);
        }else{
          //All good, return OK message
          echo 200;
          return;
        }
      }else{
              $sql=" 
      START TRANSACTION; UPDATE leltar SET leltar.Status=2, leltar.RentBy=NULL WHERE leltar.Nev IN (".$dataString.");";
      $sql.="INSERT INTO takelog VALUES";
    foreach($data as $d){
      $sql.="(NULL, '1', '$currDate', '$userName', '$d', 'IN',0,NULL),";
    }
      //Removes last comma from sql command.
      $sql=substr_replace($sql, "", -1);
      $sql.="; COMMIT;";
      if(!$connection->multi_query($sql)){
        printf("Error message: %s\n", $connection->error);
      }else{
        //All good, return OK message
        echo 200;
        return;
      }
      }


  }

  //Usercheck approved retrieve process. Acknowledges the takeout process and sets the item status to 1 (taken out)
  static function approveRetrieve($value){
    /*  If not approved (value=false) - RETRIEVE CANNOT BE declined!  */
    /*  If approved (value=true) - RETRIEVE CANNOT BE declined!  */
    $userName = $_SESSION['UserUserName'];
    if(empty($userName)){
      return 400; // Session data is empty (e.g User is not loggged in.)
    }else{
      $data = json_decode(stripslashes($_POST['data']));
      $dataArray=array();
      foreach($data as $d){
      array_push($dataArray,$d);
    }
      //For Use in the SQL query.
      $dataString = "'" . implode ( "','", $dataArray ) . "'";

      //Restore Items allowing others to take it out.
      $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=1 WHERE leltar.Nev IN (".$dataString.");";
      //Acknowledge events in log.
      $sql.="UPDATE takelog SET Acknowledged=1, ACKBY='".$userName."' WHERE User='".$_POST['user']."' AND Date='".$_POST['date']."' AND EVENT='IN' AND Item IN (".$dataString."); COMMIT;";

      $connection=Database::runQuery_mysqli();
      if(!$connection->multi_query($sql)){
        echo "Error message: %s\n".$connection->error;
      }else{
        //All good, return OK message
        //////echo $sql;
        echo 200;
        return;
      }
    }

    /*  If approved (value=true)  */
  }
}

class itemDataManager{
    static function getNumberOfTotalItems(){}
    static function getNumberOfTakenItems(){}
    static function getItemData($itemTypes){
        $displayed="";
        if ($itemTypes['rentable']!=1 & $itemTypes['studio']!=2 & $itemTypes['nonRentable']!=3 & $itemTypes['Out']!=4 ){
            return NULL;
        }
        $sql='';
        //Kölcsönözhető
        if ($itemTypes['rentable']==1){
          $sql .= 'SELECT * FROM leltar WHERE TakeRestrict=""';
          $displayed=$displayed." Kölcsönözhető";
        }
        //Stúdiós
        if ($itemTypes['studio']==2){
          if (isset($_GET['rentable'])){
            $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="s"';
            $displayed=$displayed.", Stúdiós";
          }else{
            $sql = 'SELECT * FROM leltar WHERE TakeRestrict="s"';
            $displayed=$displayed." Stúdiós";
          }
          
        }
        //Nem kölcsönözhető
        if ($itemTypes['nonRentable']==3){
          //Speciális eset, ha csak a nem kölcsönözhető, stúdiós elemeket akarjuk kilistázni
              if (isset($_GET['rentable']) || isset($_GET['studio'])){
                $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="*"';
                $displayed=$displayed.", Nem kölcsönözhető";
              }else{
                $sql =' SELECT * FROM leltar WHERE TakeRestrict="*"';
                $displayed=$displayed."Nem kölcsönözhető";
              }

        }
        //Kinnlevő
        if ($itemTypes['Out']==4){
          if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['nonRentable'])){
            $sql .= 'UNION SELECT * FROM leltar WHERE RentBy IS NOT NULL';
            $displayed=$displayed.", Kinnlevő";
          }else{
            $sql = 'SELECT * FROM leltar WHERE RentBy IS NOT NULL';
            $displayed=$displayed."Kinnlevő";
          }
        }
        $sql= $sql." ORDER BY ".$_GET['orderByField']." ".$_GET['order'];
        // echo $sql;
        return Database::runQuery($sql);
    }
    static function generateTakeoutJSON(){
      $mysqli = Database::runQuery_mysqli();
      $rows = array();
      $mysqli->set_charset("utf8");
      $query = "SELECT Nev, ID, UID, Category, TakeRestrict, Status FROM leltar"; //AND Status=1 
      if ($result = $mysqli->query($query)) {
          while ($row = $result->fetch_assoc()) {
              if($row['Status']!="1"){
                  $row['state']=['disabled' => true];
              }else{
                  $row['state']=['disabled' => false];
              }
              $rows[] = $row;

      }
      $a=json_encode($rows);
          //var_dump($a);
          $itemsJSONFile = fopen('./data/takeOutItems.json', 'w');
          fwrite($itemsJSONFile, $a);
          fclose($itemsJSONFile);
      }
      return;
    }

}

class itemHistoryManager{

}

if(isset($_POST['mode'])){

  //Set timezone to the computer's timezone.
  date_default_timezone_set('Europe/Budapest');

  if($_POST['mode']=='takeOutStaging'){
    echo takeOutManager::stageTakeout();
    //Header set.
    exit();
  }
  if($_POST['mode']=='takeOutApproval'){
    echo takeOutManager::approveTakeout($_POST['value']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if($_POST['mode']=='retrieveStaging'){
    echo retrieveManager::stageRetrieve();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  if($_POST['mode']=='retrieveApproval'){
    echo retrieveManager::approveRetrieve($_POST['value']);
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  
}

?>