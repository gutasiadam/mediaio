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
          $data = json_decode(stripslashes($_POST['data']),true);
          $dataArray=array();
          foreach($data as $d){
            array_push($dataArray,$d["name"]);
          }
          //For Use in the SQL query.
          $dataString = "'" . implode ( "','", $dataArray ) . "'";
          //Restore Items allowing others to take it out.
          $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=0 AND RentBy='".$_POST['user']."' WHERE leltar.Nev IN (".$dataString.");";
          //Acknowledge events in log.
          $sql.="UPDATE takelog SET Acknowledged=1, ACKBY='".$userName."' WHERE User='".$_POST['user']."' AND Date='".$_POST['date']."' AND EVENT='OUT' AND JSON_CONTAINS(Items, '".$_POST['data']."'); COMMIT;";
      
          echo $sql;
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
          $data = json_decode(stripslashes($_POST['data']),true);
          $dataArray=array();
          foreach($data as $d){
            array_push($dataArray,$d["name"]);
          }
          // var_dump($dataArray);
          //Preparing items for dataset in sql command.
          $dataString = "'" . implode ( "','", $dataArray ) . "'";
      
          //Restore Items allowing others to take it out.
          $sql="START TRANSACTION; UPDATE leltar SET Status=1, RentBy='NULL' WHERE Nev IN (".$dataString.");";
          //Remove takeOut event form log.
          $sql.="DELETE FROM takelog WHERE Acknowledged=0 AND User='".$_POST['user']."'AND Event='OUT' AND JSON_CONTAINS(Items, '".$_POST['data']."') AND Date='".$_POST['date']."'; COMMIT;";
          
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
      $data = json_decode(stripslashes($_POST['data']));
      $dataArray=array();
      foreach($data as $d){
        //push 
      array_push($dataArray,array("name" => $d));
    }
    //var_dump($dataArray);
    //For Use in SQL query.
    
    $itemNamesString='';
    foreach ($dataArray as $array) {
      $itemNamesString=$itemNamesString."'".$array['name']."',";
    }

    //strip last comma
    $itemNamesString=substr($itemNamesString,0,-1);
    // //Convert DataArraz to JSON
    $dataString = json_encode($dataArray);
    // Database init  - create a mysqli object
      
      $connection=Database::runQuery_mysqli();
      if(in_array("admin",$_SESSION['groups'])){//Auto accept 
        $sql=" START TRANSACTION; UPDATE leltar SET leltar.Status=1, leltar.RentBy=NULL WHERE leltar.Nev IN (".$itemNamesString.");";
        $sql.="INSERT INTO takelog VALUES";
        $sql.="(NULL, '$currDate', '$userName', '".$dataString."', 'IN',1,'$userName')";
        $sql.="; COMMIT;";;
        if(!$connection->multi_query($sql)){
           printf("Error message: %s\n", $connection->error);
         }else{
            //All good, return OK message
            echo 200;
            exit();
            return;
          }
      }else{ // Manual accept in usercheck required
              $sql=" 
      START TRANSACTION; UPDATE leltar SET leltar.Status=2, leltar.RentBy=NULL WHERE leltar.Nev IN (".$itemNamesString.");";
      $sql.="INSERT INTO takelog VALUES";
      $sql.="(NULL, '$currDate', '$userName', '".$dataString."', 'IN',0,NULL)";
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
      $data = json_decode(stripslashes($_POST['data']),true);
      $dataArray=array();

      foreach($data as $d){
        array_push($dataArray,$d["name"]);
      }
      //For Use in the SQL query.
      $dataString = "'" . implode ( "','", $dataArray ) . "'";

      //Restore Items allowing others to take it out.
      $sql="START TRANSACTION; UPDATE leltar SET leltar.Status=1 WHERE leltar.Nev IN (".$dataString.");";
      //Acknowledge events in log.
      $sql.="UPDATE takelog SET Acknowledged=1, ACKBY='".$userName."', Date='".date("Y/m/d H:i:s")."' WHERE User='".$_POST['user']."' AND Date='".$_POST['date']."' AND EVENT='IN' AND JSON_CONTAINS(Items, '".$_POST['data']."'); COMMIT;";

      $connection=Database::runQuery_mysqli();
      if(!$connection->multi_query($sql)){
        echo "Error message: %s\n".$connection->error;
      }else{
        //All good, return OK message
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
        if ($itemTypes['rentable']!=1 & $itemTypes['studio']!=2 & $itemTypes['nonRentable']!=3 & $itemTypes['Out']!=4 & $itemTypes['Event']!=5){
            return NULL;
        }
        $sql='';
        //Kölcsönözhető
        if ($itemTypes['rentable']==1){
          $sql .= 'SELECT * FROM leltar WHERE TakeRestrict=""';
          $displayed=$displayed." Médiás";
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

        //Eventes
        if ($itemTypes['Event']==5){
          if (isset($_GET['rentable']) || isset($_GET['studio']) ){
            $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="e"';
            $displayed=$displayed.", Eventes";
          }else{
            $sql =' SELECT * FROM leltar WHERE TakeRestrict="e"';
            $displayed=$displayed."eventes";
          }
        }  
        //Nem kölcsönözhető
        if ($itemTypes['nonRentable']==3){
          //Speciális eset, ha csak a nem kölcsönözhető, stúdiós elemeket akarjuk kilistázni
              if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['Event'])){
                $sql .= 'UNION SELECT * FROM leltar WHERE TakeRestrict="*"';
                $displayed=$displayed.", Nem kölcsönözhető";
              }else{
                $sql =' SELECT * FROM leltar WHERE TakeRestrict="*"';
                $displayed=$displayed."Nem kölcsönözhető";
              }

        }
        //Kinnlevő
        if ($itemTypes['Out']==4){
          if (isset($_GET['rentable']) || isset($_GET['studio']) || isset($_GET['nonRentable']) || isset($_GET['Event'])){
            $sql .= 'UNION SELECT * FROM leltar WHERE RentBy IS NOT NULL';
            $displayed=$displayed.", Kinnlevő";
          }else{
            $sql = 'SELECT * FROM leltar WHERE RentBy IS NOT NULL';
            $displayed=$displayed."Kinnlevő";
          }
        }


        $sql= $sql." ORDER BY ".$_GET['orderByField']." ".$_GET['order'];
        //echo $sql;
        return Database::runQuery($sql);
    }
    /** Generates JSON data for takeout page, showing available and unavailable items. */
    static function generateTakeoutJSON(){
      $mysqli = Database::runQuery_mysqli();
      $rows = array();
      $mysqli->set_charset("utf8");
      $query = "SELECT Nev, ID, UID, Category, TakeRestrict, ConnectsToItems, Status FROM leltar"; //AND Status=1 
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

class userManager{
  static function getUsers(){
    $mysqli = Database::runQuery_mysqli();
    $rows = array();
    $mysqli->set_charset("utf8");
    $query = "SELECT usernameUsers FROM users";
    if ($result = $mysqli->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    $a=json_encode($rows);
        //var_dump($a);
        echo $a;
    }
    return;
  }
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

  if($_POST['mode']=='getUsers'){
    echo userManager::getUsers();
    //echo $_POST['value'] ;
    //Header set.
    exit();
  }
  
}

?>